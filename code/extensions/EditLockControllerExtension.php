<?php
/**
 * EditLockControllerExtension
 *
 * @package silverstripe-editlock
 * @author shea@silverstripe.com.au
 **/
class EditLockControllerExtension extends Extension
{
    
    private static $allowed_actions = array(
        'lock'
    );


    /**
     * Updtes the edit form based on whether it is being edited or not
     **/
    public function updateForm($form, $record)
    {
        if (!$record) {
            return;
        }
        // if the current user can't edit the record anyway, we don't need to do anything
        if ($record && !$record->canEdit()) {
            return $form;
        }

        // check if this record is being edited by another user
        $beingEdited = RecordBeingEdited::get()->filter(array(
            'RecordID' => $record->ID,
            'RecordClass' => $record->ClassName,
            'EditorID:not' => Member::currentUserID()
        ))->first();

        if ($beingEdited) {
            if ($this->owner->getRequest()->getVar('editanyway') == '1') {
                $beingEdited->isEditingAnyway();
                return $this->owner->redirectBack();
            }
            // if the RecordBeingEdited record has not been updated in the last 15 seconds (via ping)
            // the person editing it must have left the edit form, so delete the RecordBeingEdited
            if (strtotime($beingEdited->LastEdited) < (time() - 15)) {
                $beingEdited->delete();
            // otherwise, there must be someone currently editing this record, so make the form readonly
            // unless they have permission to, and have chosen to edit anyway
            } else {
                if (!$beingEdited->isEditingAnyway()) {
                    $readonlyFields = $form->Fields()->makeReadonly();
                    $form->setFields($readonlyFields);
                    $form->addExtraClass('edit-locked');
                    $form->setAttribute('data-lockedmessage', $beingEdited->getLockedMessage());
                    return;
                }
            }
        }

        $form->setAttribute('data-recordclass', $record->ClassName);
        $form->setAttribute('data-recordid', $record->ID);
        $form->setAttribute('data-lockurl', $this->owner->link('lock'));
        return $form;
    }


    /**
     * Extension hook for LeftAndMain subclasses 
     **/
    public function updateEditForm($form)
    {
        if ($record = $form->getRecord()) {
            $form = $this->updateForm($form, $record);
        }
    }


    /**
     * Extension hook for GridFieldDetailForm_ItemRequest
     **/
    public function updateItemEditForm($form)
    {
        if ($record = $form->getRecord()) {
            $form = $this->updateForm($form, $record);
        }
    }


    /**
     * Handles ajax pings to create a RecordBeingEdited lock or update an existing one
     **/
    public function lock($request)
    {
        $id = (int)$request->postVar('RecordID');
        $class = $request->postVar('RecordClass');

        $existing = RecordBeingEdited::get()->filter(array(
            'RecordID' => $id,
            'RecordClass' => $class,
            'EditorID' => Member::currentUserID()
        ))->first();

        if ($existing) {
            $existing->write(false, false, true);
        } else {
            $lock = RecordBeingEdited::create(array(
                'RecordID' => $id,
                'RecordClass' => $class,
                'EditorID' => Member::currentUserID()
            ));
            $lock->write();
        }
    }
}

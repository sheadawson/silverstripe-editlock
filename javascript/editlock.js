jQuery.entwine("editlock", function($) {
	var lockTimer = 0;
	$('.cms-edit-form').entwine({
		RecordID: null,
		RecordClass: null,
		LockURL: null,
		onmatch: function() {
			// clear any previously bound lock timer
			if (lockTimer) {
				clearTimeout(lockTimer);
			}

			if(this.hasClass('edit-locked')){
				this.showLockMessage();
			}else{
				this.setRecordID(this.data('recordid'));
				this.setRecordClass(this.data('recordclass'));
				this.setLockURL(this.data('lockurl'));
				this.lockRecord();	
			}
		},
		
		lockRecord: function() {
			if(!this.getRecordID() || !this.getRecordClass() || !this.getLockURL()){
				return false;
			}
			
			var data = { 
				RecordID: this.getRecordID(), 
				RecordClass: this.getRecordClass() 
			};

			$.post(this.getLockURL(), data).done(function(result){
				lockTimer = setTimeout(function(){$('.cms-edit-form').lockRecord()},10000);
			});
		},

		showLockMessage: function(){
			this.find('p.message').first().after('<p/>')
				.addClass('message warning')
				.css('overflow', 'hidden')
				.html(this.data('lockedmessage'))
				.show();
		}
	});
});



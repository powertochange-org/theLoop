var givingpage_s = {

tranCache: {},

translate: function(){
	$('.lang-tran').each(function(){
		var word = $(this).data('tranWord');
		var locale = ptc.getLocale();
		var r;
		if(locale in givingpage_s.tranCache && word in givingpage_s.tranCache[locale]){
			r = givingpage_s.tranCache[locale][word];
			if($(this).hasClass('merge')){
				r = ptc.strFormat(r, $('#donor_fn').val());
			}
		} else{
			if($(this).hasClass('merge')){
				var guid = ptc.tranGUID ++;
				r = "<span id='tranGUID_" + guid + "'>" + word + "</span>";
				ptc.getTranslation(word, function(data){
					if(!(locale in givingpage_s.tranCache)){
						givingpage_s.tranCache[locale] = {};
					}
					givingpage_s.tranCache[locale][word] = data.d;
					$('#tranGUID_' + guid).replaceWith(data.d);
				});
			} else {
				r = ptc.getTranslation(word);
				ptc.getTranslation(word, function(data){
					if(!(locale in givingpage_s.tranCache)){
						givingpage_s.tranCache[locale] = {};
					}
					givingpage_s.tranCache[locale][word] = data.d;
				});
			}
		}
		$(this).html(r);
	});
},

balanceAmount: function(input) {
	if(input.value){
		var enteredAmount = new Number(input.value);
		if (isNaN(enteredAmount) || enteredAmount < 0) {
			enteredAmount = 0;
		}
		input.value = enteredAmount.toFixed(2);
	}
},

adjustDateView: function(showDates){
	if (showDates) {
		$('.recurring').css('display', '');
		this.changeDateRange($('[name="donationDayOfMonth"]:checked').val());
	} else {
		$('.recurring').css('display', 'none');
	}
},

getEarliestDate: function(day){
	var today = new Date();
	var dayOfMonthToday = today.getDate();
	var month = today.getMonth();
	var year = today.getFullYear();

	if (dayOfMonthToday > day) {
		if (today.getMonth() == 11) { //zero based
			year = year + 1;
			month = 0; //zero based
		} else {
			month = month + 1;
		}
	}
	return new Date(year, month, day);
},

changeDateRange: function(day) {
	date = this.getEarliestDate(day)

	// set value
	$('#txtStartDate').val((date.getMonth() + 1) /* zero based */ + '/' + date.getDate() + '/' + date.getFullYear());
},

init: function(){
	givingpage.project.display();
}
}

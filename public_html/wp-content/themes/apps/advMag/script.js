var advMag = {

	init: function(){
		this.send('getDonors', null, function(data){
			$('.advMag tbody').empty();
			if(0 == data.r.length){
				$('.advMag tbody').append("<tr><td colspan='5'>nothing found</td></tr>");
			} else {
				for(var i = 0; i < data.r.length; i ++){
					$('.advMag tbody').append(
						"<tr data-id='" + data.r[i].id + "' ><td>" + data.r[i].name + "</td>" + //todo
						"<td>" + advMag.printLanguageSelection(data.r[i].lang) + "</td>" +
						"<td>" + advMag.printMagazineSelection(data.r[i].mag) + "</td>" +
						"<td><button class='lang' disabled='disabled'>Save</button></td></tr>"
					)
				}
				advMag.addActionListeners();
			}
		}, function(){
			$('.advMag tbody').empty();
			$('.advMag tbody').append("<tr><td colspan='5'>an error occurred</td></tr>");
		});
	},
	
	addActionListeners: function(address){
		$('body').on('change', '.advMag input, .advMag select', function(){
			//first see if dirty
			var d = false;
			var row = $(this).closest('tr');
			row.find('input, select').each(function(){
				if($(this).val() != $(this).data('orgval')){
					d = true;
					return false;
				}
			});
			if(d){
				row.addClass('dirty');
				row.find('button').removeAttr('disabled');
			} else {
				row.removeClass('dirty');
				row.find('button').attr('disabled', 'disabled');
			}
			if(0 == $('.advMag .dirty').length){
				$('.advMag th button').attr('disabled', 'disabled');
			} else {
				$('.advMag th button').removeAttr('disabled');
			}
			row.removeClass('saved');
			row.removeClass('error');
		});
		$('body').on('click', '.advMag td button', function(){
			advMag.save($(this).closest('tr'));
		});
		$('body').on('click', '.advMag th button', function(){
			$(this).closest('table').find('tbody tr.dirty').each(function(){
				advMag.save(this);
			});
		});
		/*$('.advMag td span').click(function(){
			if(0 == $(this).children().length){
				var h = $(this).html();
				$(this).html("<input data-orgval='" + h + "' value='" + h + "' />");
			}
		});*/
	},
	
	save: function(row){
		//first see if dirty
		var d = false;
		var data = {id: $(row).data('id')};
		var success = [];
		
		$(row).removeClass('saved');
		$(row).removeClass('error');
		
		$(row).find('input, select').each(function(){
			if($(this).val() != $(this).data('orgval')){
				d = true;
				data[$(this).data('field')] = $(this).val();
				success.push({field: $(this), val: $(this).val()});
			}
		});
		if(d){
			this.send('sendInfo', data, function(){
				console.log('s' + data.id);
				for(var i = 0; i < success.length; i ++){
					success[i].field.data('orgval', success[i].val);
				}
				$(row).removeClass('dirty');
				$(row).addClass('saved');
				$(row).find('button').attr('disabled', 'disabled');
			}, function(){
				console.log('e' + data.id);
				$(row).addClass('error');
			});
		}
	},
	
	printAddress: function(address){
		return "<input placeholder='Line4' data-field='line4' data-orgval='" + address.line4 + "' value='" + address.line4 + "' /><br />" +
			"<input placeholder='Line1' data-field='line1' data-orgval='" + address.line1 + "' value='" + address.line1 + "' /><br />" +
			"<input placeholder='Line2' data-field='line2' data-orgval='" + address.line2 + "' value='" + address.line2 + "' /><br />" +
			"<input placeholder='Line3' data-field='line3' data-orgval='" + address.line3 + "' value='" + address.line3 + "' /><br />" +
			"<input placeholder='City' data-field='city' data-orgval='" + address.city + "' value='" + address.city + "' /><br />" +
			"<input placeholder='Province' data-field='state' data-orgval='" + address.state + "' value='" + address.state + "' /><br />" +
			"<input placeholder='Postal Code' data-field='postalCode' data-orgval='" + address.postalCode + "' value='" + address.postalCode + "' /><br />" +
			"<input placeholder='Country' data-field='country' data-orgval='" + address.country + "' value='" + address.country + "' />";
		
		/*<label class='lang'>Line 4</label>: <span data-field='line4'>" + address.line4 + '</span><br />' +
			"<label class='lang'>Line 1</label>: <span data-field='line1'>" + address.line1 + '</span><br />' +
			"<label class='lang'>Line 2</label>: <span data-field='line2'>" + address.line2 + '</span><br />' +
			"<label class='lang'>Line 3</label>: <span data-field='line3'>" + address.line3 + '</span><br />' +
			"<label class='lang'>City</label>: <span data-field='city'>" + address.city + '</span><br />' +
			"<label class='lang'>Province</label>: <span data-field='state'>" + address.state + '</span><br />' +
			"<label class='lang'>Postal Code</label>: <span data-field='postalCode'>" + address.postalCode + '</span><br />' +
			"<label class='lang'>Country</label>: <span data-field='country'>" + address.country + '</span>';*/
		
	},
	
	printMagazineSelection: function(value){
		return "<select data-field='MAGAZINE' data-orgval='" + value + "'>" +
			"<option value=''></option>" +
			"<option value='STA_EMAIL' class='lang'" + ('STA_EMAIL' == value ? " selected='selected'" : '') + ">Staff Emails</option>" +
			"<option value='HQ_EMAIL' class='lang'" + ('HQ_EMAIL' == value ? " selected='selected'" : '') + ">HQ Emails</option>" +
			"<option value='STA_DELIVE' class='lang'" + ('STA_DELIVE' == value ? " selected='selected'" : '') + ">Staff Delivers</option>" +
			"<option value='HQ_DELIVE' class='lang'" + ('HQ_DELIVE' == value ? " selected='selected'" : '') + ">HQ Mails</option>" +
			"<option value='NONE' class='lang'" + ('NONE' == value ? " selected='selected'" : '') + ">No Magazine</option>" +
		"</select>";
	},
	
	printLanguageSelection: function(value){
		return "<select data-field='DDCLANG' data-orgval='" + value + "'>" +
			"<option value='' class='lang'>Language</option>" +
			"<option value='E' class='lang'" + ('E' == value ? " selected='selected'" : '') + ">English</option>" +
			"<option value='F' class='lang'" + ('F' == value ? " selected='selected'" : '') + ">French</option>" +
			"<option value='PF' class='lang'" + ('PF' == value ? " selected='selected'" : '') + ">Prefer French</option>" +
		"</select>";
	},
	
	getTranslation: function(phrase, callback, refreshGuid){
		if ('undefined' == typeof refreshGuid){
			guid = globalGUID ++;
		} else {
			guid = refreshGuid;
		}
		
		if ('undefined' == typeof callback || null == callback){
			callback = function(data){
				if(0 == $('#tranGUID_' + guid).length){
					//console.log('cannotFind:' + guid + ":" + data);
				}
				$('#tranGUID_' + guid).html(data.r);
			}
		}
		var out = phrase;
		module = getParameterByName('strings');
		if(!module){
			module = window.location.hash.substring(1);
		}
		if(!(module in strings)){
			console.log('FATAL ERROR: ' + module + ' not loaded.  Looking for: ' + phrase);
			fatalError(null, true);
			return;
		}
		
		if(phrase in strings[module]){
			out = strings[module][phrase];
		} else if(phrase in strings['']){
			out = strings[''][phrase];			
		}
		callback({r: out});
		return out;
		
		/*send({module: "", action : "tran", phrase : phrase}, callback, function(){}, 'GET');
		return "<span id='tranGUID_" + guid + "'>" + phrase + "</span>";*/
	},
	
	send: function(fun, data, success, fail){
		if ('undefined' == typeof fail){
			fail = function(){};
		}
		if ('undefined' == typeof success){
			success = function(data){console.log(data)};
		}
		if ('undefined' == typeof data || null == data){
			data = {};
		}
		
		data.action = 'advmag_' + fun,
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: success,
			error: fail
		});
	}
}
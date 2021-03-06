var givingpage = {
ajaxurl: '',
project: null,
host: '',
resize: null,
guid: 0,
jCrop: null,

override: function(){

	//extending display function
	(function(display) {
		ptc_op.Project.prototype.display = function () {
			givingpage_s.translate();
			display.call(this);
			ptc_op.displaying = null;
			givingpage.updateLetter();
		};
	}(ptc_op.Project.prototype.display));
	
	ptc_op.Project.prototype.getLogo = function(locale){
		if('undefined' == typeof locale){
			locale = ptc.getLocale();
		}
		var l = '';
		if('en-US' != locale){
			l = '.' + locale;
		}
		return givingpage.getURL("/images/product/icon/" + this.filename + l + ".jpg");
	}

	ptc_op.Project.prototype.getPicture = givingpage.getPicture

	ptc_op.Project.prototype.getBackground = function(){
		return givingpage.getURL("/images/product/large/" + this.filename + ".jpg");
	}
	
	ptc_op.Project.prototype.getLink = function(){
		return givingpage.getURL("/p-" + this.getHash() + ".aspx");
	}

	ptc_op.Category.prototype.getLogo = function(locale){
		if('undefined' == typeof locale){
			locale = ptc.getLocale();
		}
		var l = '';
		if('en-US' != locale){
			l = '.' + locale;
		}
		return givingpage.getURL("/images/category/icon/" + this.filename + l + ".jpg");
	}

	ptc_op.Category.prototype.getPicture = function(){
		return givingpage.getURL("/images/category/medium/" + this.filename + ".jpg");
	}

	ptc_op.Category.prototype.getBackground = function(){
		return givingpage.getURL("/images/category/large/" + this.filename + ".jpg");
	}
	
	ptc.webservice = givingpage.send;
},

getURL: function(path){
	return this.host + path;
},

resizeImage: function(image64, x, y, w, h){
	var canvas = $('<canvas></canvas>')[0];
	var image = $('<img />')[0];
	image.src = image64;
	//fix the size to square 350px
	canvas.width = 350;
	canvas.height = 350;
	//350 is output size and 150 is the input size (for the width) is scaled to that by css
	canvas.getContext('2d').drawImage(image, -x * 350 / w, -y * 350 / h, 150 * 350 / w, image.naturalHeight * 150 / image.naturalWidth * 350 / h);
	return canvas.toDataURL("image/png");
},

getPicture: function(){
	if($('.input .closed').is(':checked')){
		return givingpage.getURL("/images/product/medium/" + this.gender + ".jpg");
	} else if(null == givingpage.resize){
		return givingpage.getURL("/images/product/medium/" + this.filename + ".jpg" + "?r=" + Math.random());
	} else{
		return givingpage.resizeImage($('.input .image + img').attr('src'), givingpage.resize.x, givingpage.resize.y, givingpage.resize.w, givingpage.resize.h)
	}
},

setDescription: function(){
	if(!$('.input .closed').is(':checked')){
		var p = givingpage.project;
		p.description = '<ml><locale name="en-US">' + $('.input .description').val().replace(/<(?:.|\n)*?>/gm, '').replace('\n', '&lt;br /&gt;') + '</locale>' + 
			'<locale name="fr-CA">' + $('.input .description-french').val().replace(/<(?:.|\n)*?>/gm, '').replace('\n', '&lt;br /&gt;') + '</locale></ml>';
		p.display();
	}
},

setEAck: function(){
	var p = givingpage.project;
	p.eAck = '<ml><locale name="en-US">' + $('.input .eAck').val().replace(/<(?:.|\n)*?>/gm, '') + '</locale>' + 
		'<locale name="fr-CA">' + $('.input .eAck-french').val().replace(/<(?:.|\n)*?>/gm, '') + '</locale></ml>';
	givingpage.updateLetter();
},

send: function(method, fun, data, success, fail){
	if ('undefined' == typeof fail){
		fail = function(){};
	}
	if ('undefined' == typeof success){
		success = function(data){console.log(data)};
	}
	if ('undefined' == typeof data || null == data){
		data = {};
	}
	
	data.action = 'advgp_' + fun,
	$.ajax({
		type: "POST",
		url: givingpage.ajaxurl,
		data: data,
		success: success,
		error: fail
	});
},

getStaffProjectBySKU: function(sku){
	var all = ptc_op.getStaffItems();
	for(var i = 0; i < all.length; i ++){
		if(sku == all[i].p.sku){
			return all[i].p;
		}
	}
},

updateLetter: function(){
	$('#letter .staffPic').attr("src", $('#project_pic').attr("src"));
	var letter = ptc_op.parseXML(this.project.eAck).replace(/(?:\r\n|\r|\n)/g, '<br />');
	if(letter){
		$('#letter #staffLetter').html(letter);
	} else {
		$('#letter #staffLetter').html(
			"<p><span class='lang-tran' data-tran-word='ptc.letterBodyStart'></span>" +
			"<span class='lang-tran' data-tran-word='ptc.letterBodyDonation'></span>" +
			"<span class='lang-tran' data-tran-word='ptc.letterBodyEnd'></span></p>" +
			"<p class='lang-tran' data-tran-word='ptc.taxReceiptIssue'></p>" +
			"<p class='lang-tran' data-tran-word='ptc.signature'></p>" +
			"<p><span class='lang-tran' data-tran-word='ptc.letter.sigName'></span><br />" +
			"<span class='lang-tran' data-tran-word='ptc.president'></span></p>"
		);
		givingpage_s.translate();
	}

	var thankyouURL = givingpage.getURL('/images/email/sty.png');
	$('#letter .staffPic + img').attr('src', thankyouURL);
	if('en-US' != ptc.getLocale()){
		thankyouURL = givingpage.getURL('/images/email/sty.' + ptc.getLocale() + '.png');;
		ptc_op.urlExists(thankyouURL, function(){
			//success (image exists in current locale);
			$('#letter .staffPic + img').attr("src", thankyouURL);
		}, function(){
			//fail (image does not exists in current locale)
			//do nothing
		});
	}
},

setImage(image64){
	$('.input .image + img').attr('src', image64);
	jQuery(function($) {
		if(null != givingpage.jCrop){
			givingpage.jCrop.destroy();
			$('.input .image + img').css('display', '');
			$('.input .image + img').css('height', '');
		}
		//givingpage.jCrop = $.Jcrop('.input .image + img', {
		$('.input .image + img').Jcrop(
			{
				aspectRatio: 1,
				setSelect: [0, 0, 150, 150],
				onSelect: function(c){
					console.log('select');
					givingpage.resize = c;
					givingpage.project.display();
				}
			},
			function(){ givingpage.jCrop = this}
		);
	});
},

init: function(host){
	ptc_op.initProjectMethods();
	ptc_op.initCategoryMethods();
	this.host = host
	//ptc.domain = host;
	this.override();
	this.send('POST', 'GetInfo', null, function(data){
		if("" == data.r.pc){
			$('.input').next().remove();
			$('.input').replaceWith("No projectcode found.");
			$('#sample').remove();
			return;
		}
		givingpage.project = givingpage.getStaffProjectBySKU(data.r.pc);
		var p = givingpage.project;
		if('undefined' == typeof p){
			$('.input').next().remove();
			$('.input').replaceWith("Error: No giving page found or something is not set up correctly.  If you do have a giving page, please contact: <a href='mailto:helpdesk@p2c.com?subject=Loop-Givingpage&body=Name:" + ptc_op.parseXML(data.r.name) + ",%20Projectcode:" + data.r.pc  +"'>helpdesk@p2c.com</a><br /><br />");
			$('#sample').remove();
			return;
		}
		p.label_old = data.r.name;
		p.cats_old = [];
		for(var i = 0; i < data.r.cats.length; i ++){
			p.cats_old.push(new ptc_op.Category(data.r.cats[i]));
		}
		
		p.gender = data.r.gender;
		
		//set inputs;
		$('.input .projectcode').html(p.sku);
		$('.input .link').html(p.getLink());
		$('.input .link').attr('href', p.getLink());
		if('onetime' in p.data){
			$('.input .frequency.ot').prop('checked', true);
			$('.input .amount').val(p.data.onetime);
		}
		else if('recurring' in p.data){
			$('.input .frequency.m').prop('checked', true);
			$('.input .amount').val(p.data.recurring);
		} else {
			//default
			$('.input .frequency.m').prop('checked', true);
			$('.input .amount').val('60');
		}
		$('.input .image + img').attr('src', p.getPicture());
		$('.input .closed').prop('checked', p.getName() == p.sku);
		$('.input .description').val(ptc_op.parseXML(p.description, 'en-US').replace('<br />', String.fromCharCode(10)));
		$('.input .description-french').val(ptc_op.parseXML(p.description, 'fr-CA').replace('<br />', String.fromCharCode(10)));
		
		if(data.r.eAcks){
			$('.input .eAck').val(data.r.eAcks['en-US']);
			$('.input .eAck-french').val(data.r.eAcks['fr-CA']);
			givingpage.setEAck();
		}
		
		givingpage_s.init('https://secure.powertochange.org');
	});
	
	$('.input .amount').change(function(){
		var p = givingpage.project;
		if('onetime' in p.data){
			p.data.onetime = $(this).val();
		}
		else if('recurring' in p.data){
			p.data.recurring = $(this).val();
		} else {
			//default
			p.data.recurring = $(this).val();
		}
		p.display();
	});
	$('.input .frequency').change(function(){
		var p = givingpage.project;
		var v = $('.input .frequency:checked').val();
		if('ot' == v){
			delete p.data.recurring;
			p.data.onetime = $('.input .amount').val();
		}
		else if('m' == v){
			delete p.data.onetime;
			p.data.recurring = $('.input .amount').val();
		} else {
			//default
			delete p.data.onetime;
			delete p.data.recurring;
		}
		p.display();
	});
	$('.input .image').change(function(){
		var input = $(this)[0];
		$(this).css('cursor', 'wait');
		file = input.files[0];
		
		//if the user cancels
		if('undefined' == typeof file){
			$('.input .image').css('cursor', '');
			return;
		}
		$('.rotate').show();
		var fr = new FileReader();
		fr.onload = function(){
			$('.input .image').css('cursor', '');
			givingpage.setImage(fr.result);
		};
		fr.readAsDataURL(file);
	});
	$('.input .closed').change(function(){
		var p = givingpage.project;
		if($(this).is(':checked')){
			p.label_old = p.label;
			p.label = p.sku;
			p.data.logo = 0;
			
			//remove for ministry(ies) category and put them in international category
			var cats = p.getAllCatergoryByProject();
			p.cats_old = cats;
			allItems[allItems.inter].items.push(p.id);
			for(var i = 0; i < cats.length; i++){
				var a = allItems[cats[i].id].items;
				a.splice($.inArray(p.id, a), 1);
			}
			p.description_old = p.description;
			p.description = '';
		} else {
			p.data.logo = 1;
			if('label_old' in p){
				p.label = p.label_old;
			}
			var a = allItems[allItems.inter].items;
			a.splice($.inArray(p.id, a), 1);
			if('cats_old' in p){
				for(var i = 0 ; i < p.cats_old.length; i ++){
					allItems[p.cats_old[i].id].items.push(p.id);
				}
			}
			if('description_old' in p){
				p.description = p.description_old;
			}
		}
		givingpage.project.display();
	});
	$('.input .description, .input .description-french').change(givingpage.setDescription);
	$('.input .eAck, .input .eAck-french').change(givingpage.setEAck);
	$('.input .rotate').click(function(){
		var rot = $(this);
		rot.css('cursor', 'wait');
		rot.attr('disabled', 'disabled');
		rot.html("Rotating");
		setTimeout(function(){
			var canvas = $('<canvas></canvas>')[0];
			var image = $('.input .image + img')[0];
			canvas.width = image.naturalHeight;
			canvas.height = image.naturalWidth;
			
			var context = canvas.getContext('2d');
			context.translate(image.naturalHeight, 0);
			context.rotate(Math.PI / 2);
			context.drawImage(image, 0, 0, image.naturalWidth, image.naturalHeight);
			context.rotate(-Math.PI / 2);
			context.translate(image.naturalHeight, 0);
			givingpage.setImage(canvas.toDataURL("image/png"));
			rot.css('cursor', '');
			rot.removeAttr('disabled');
			rot.html("Rotate");
		}, 0);
	});
	$('.input .save').click(function(){
		var d = {};
		var p = givingpage.project;
		if($('.input .closed').is(':checked')){
			d.closed = 1;
		} else {
			d.closed = 0;
			d.des = $('.input .description').val();
			d.desFre = $('.input .description-french').val();
			if(null == givingpage.resize){
				d.isPic = 0;
			} else {
				d.isPic = 1;
				d.pic = givingpage.resizeImage($('.input .image + img').attr('src'), givingpage.resize.x, givingpage.resize.y, givingpage.resize.w, givingpage.resize.h)
			}
		}
		d.eAck = $('.input .eAck').val();
		d.eAckFre = $('.input .eAck-french').val();
		if('onetime' in p.data){
			d.onetime = p.data.onetime;
		}
		else if('recurring' in p.data){
			d.recurring = p.data.recurring;
		}
		var g = givingpage.guid ++;
		givingpage.send('POST', 'SetInfo', d, function(data){
			$('.input #msg_' + g).html("Changes Saved!");
		}, function(){
			$('.input #msg_' + g).html("An error occurred");
		});
		$(this).after("<div id='msg_" + g + "'>Saving...</div>");
	});
	$('.preview').change(function(){
		ptc_currentLocale = $('.preview:checked').val();
		givingpage.project.display();
	});
}
}

function postTopics(){ /* dummy function */}
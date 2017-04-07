var givingpage = {
ajaxurl: '',
project: null,
host: '',
resize: null,

override: function(){

	//extending display function
	(function(display) {
		ptc_op.Project.prototype.display = function () {
			display.call(this);
			ptc_op.displaying = null;
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
	//350 is output size and 150 is the input size is scaled to that by css
	canvas.getContext('2d').drawImage(image, -x * 350 / w, -y * 350 / h, 150 * 350 / w + 1, image.naturalHeight * 150 / image.naturalWidth * 350 / h + 1);
	return canvas.toDataURL("image/png");
},

getPicture: function(){
	if($('#input .closed').is(':checked')){
		return givingpage.getURL("/images/product/medium/" + this.gender + ".jpg");
	} else if(null == givingpage.resize){
		return givingpage.getURL("/images/product/medium/" + this.filename + ".jpg");
	} else{
		return givingpage.resizeImage($('#input .image + img').attr('src'), givingpage.resize.x, givingpage.resize.y, givingpage.resize.w, givingpage.resize.h)
	}
},

setDescription: function(){
	if(!$('#input .closed').is(':checked')){
		var p = givingpage.project;
		p.description = '<ml><locale name="en-US">' + $('#input .description').val().replace(/<(?:.|\n)*?>/gm, '') + '</locale>' + 
			'<locale name="fr-CA">' + $('#input .description-french').val().replace(/<(?:.|\n)*?>/gm, '') + '</locale></ml>';
		p.display();
	}
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

init: function(host){
	ptc_op.initProjectMethods();
	ptc_op.initCategoryMethods();
	this.host = host
	this.override();
	this.send('GetInfo', null, function(data){
		givingpage.project = givingpage.getStaffProjectBySKU(data.r.pc);
		var p = givingpage.project;
		p.gender = data.r.gender;
		//set inputs;
		$('#input .projectcode').html(p.sku);
		$('#input .link').html(p.getLink());
		if('onetime' in p.data){
			$('#input .frequency.ot').prop('checked', true);
			$('#input .amount').val(p.data.onetime);
		}
		else if('recurring' in p.data){
			$('#input .frequency.m').prop('checked', true);
			$('#input .amount').val(p.data.recurring);
		} else {
			//default
			$('#input .frequency.m').prop('checked', true);
			$('#input .amount').val('60');
		}
		$('#input .image + img').attr('src', p.getPicture());
		$('#input .closed').prop('checked', p.getName() == p.sku);
		$('#input .description').val(ptc_op.parseXML(p.description, 'en-US'));
		$('#input .description-french').val(ptc_op.parseXML(p.description, 'fr-CA'));
		givingpage_s.init('https://secure.powertochange.org');
	});
	
	$('#input .amount').change(function(){
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
	$('#input .frequency').change(function(){
		var p = givingpage.project;
		var v = $('#input .frequency:checked').val();
		if('ot' == v){
			delete p.data.recurring;
			p.data.onetime = $('#input .amount').val();
		}
		else if('m' == v){
			delete p.data.onetime;
			p.data.recurring = $('#input .amount').val();
		} else {
			//default
			delete p.data.onetime;
			delete p.data.recurring;
		}
		p.display();
	});
	$('#input .image').change(function(){
		var input = $(this)[0];
		$(this).css('cursor', 'wait');
		file = input.files[0];
		var fr = new FileReader();
		fr.onload = function(){
			$('#input .image').css('cursor', '');
			$('#input .image + img').attr('src', fr.result);
			jQuery(function($) {
				$('#input .image + img').Jcrop({
					aspectRatio: 1,
					setSelect: [0, 0, 150, 150],
					onSelect: function(c){
						console.log('select');
						givingpage.resize = c;
						givingpage.project.display();
					}
				});
			});
		};
		fr.readAsDataURL(file);
	});
	$('#input .closed').change(function(){
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
	$('#input .description, #input .description-french').change(givingpage.setDescription);
	$('#input .preview').change(function(){
		ptc_currentLocale = $('#input .preview:checked').val();
		givingpage.project.display();
	});
}
}
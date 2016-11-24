<?php
global $wpdb;
$sql = "SELECT photo
		FROM employee
		WHERE share_photo = '1' AND photo IS NOT NULL
		";

$result = $wpdb->get_results($sql, ARRAY_A);
$random = 30;
$photos = [];
for($i = 0; $i < $random; $i++) {
	$num = rand(0, count($result)-1);
	$photos[$i] = $result[$num]['photo'];
}

// foreach($result as $row) {
//     echo $row['photo'].'<br>';
// }


?>



<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>

<style type="text/css">
.snowflake {
  -webkit-animation: spin 19s linear infinite;
  -moz-animation: spin 19s linear infinite;
  animation: spin 19s linear infinite;
}
.snowflake img {
  border-radius: 30px;
  ;
}

.snowthumb {
	display: inline-block;
	width: 60px;
	height: 60px;
	/*margin: 5px;*/
	border-radius: 30px;
	/*border: 3px solid #c99;*/
	background-position: center center;
	background-size: cover;
	z-index: 9999;
	position: absolute;
	top:40px;
	left:40px;
}

#main {
	width: 700px;
	height: 400px;
	background-color: black;
}

 @-moz-keyframes 
spin { 100% {
-moz-transform: rotate(360deg);
}
}
 @-webkit-keyframes 
spin { 100% {
-webkit-transform: rotate(360deg);
}
}
 @keyframes 
spin { 100% {
-webkit-transform: rotate(360deg);
transform:rotate(360deg);
}
}
</style>
<div id="main"  style="text-align: center;"><h1 style="color:#fff; text-align:center;font-size:34px;">Merry Christmas</h1><h1 style="color:#fff; text-align:center;font-size:20px;">from your I.T. team!</h1>
<span><a href="/staff-directory/?page=myprofile">Upload</a> a photo if you want to see yourself here :)</span></div>
<script>
			jQuery(function() {
				jQuery("body").snow({
					intensity: 3,
					sizeRange: [12, 30],
					opacityRange: [0.4, 1],
					driftRange: [-10, 30],
					speedRange: [40, 80]
				});
			});
		</script><script type="text/javascript">

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {

	var SnowFlake = function(expireCallback) {
		var that = this;
		var vector = [0, 0];
		var position = [0, 0];
		var isOnscreen = false;
		
		
		var pics = [
		<?php
		for($i = 0; $i < count($photos); $i++) {
			if($i != 0)
				echo ',';
			echo '"'.$photos[$i].'"';
		}
		
		?>
		];
		
		var rand = Math.floor((Math.random() * <?php echo $random;?>));
		//console.log("rand:"+rand);
		var $element;
		
		if(Math.floor(Math.random() * 3) < 2) {
			$element = $('<div class="snowflake" style="position: absolute; color:#fff; text-shadow: rgba(0, 0, 0, 0.7) 1px 1px 2px;">&#10052;</div>');
			
		} else {
			//$element = $('<div class="snowflake" style="position: fixed; color:#fff;text-shadow: rgba(0, 0, 0, 0.7) 1px 1px 2px;"><img src="/wp-content/images/flake2_140.png" style="position:fixed;"/><img src="https://staff.powertochange.org/wp-content/uploads/staff_photos/'+pics[rand]+'" width="60px" height="52px" style="position:fixed;left:40px;top:45px;"/></div>');
			
			$element = $('<div class="snowflake" style="position: absolute; color:#fff;text-shadow: rgba(0, 0, 0, 0.7) 1px 1px 2px;background-image: url(\'/wp-content/images/flake2_140.png\');width:140px;height:140px;">' +
					'<div class="snowthumb" style="background-image: url(\'https://staff.powertochange.org/wp-content/uploads/staff_photos/'+pics[rand]+'\')"></div>' + 
					
					'</div>');
		}
		

		var updatePosition = function() {
			$element.css({
				left: position[0],
				top: position[1]
			});
		};

		var updateAttributes = function(size, opacity) {
			$element.css({
				"font-size": size,
				opacity: opacity
			});
		};

		var checkExpired = function(bounds) {
			if (!(position[0] > bounds.x && position[0] < bounds.x2 && position[1] > bounds.y && position[1] < bounds.y2)) {
				isOnscreen = false;
				$element.remove();
				expireCallback(that);
			}
		};

		this.spawn = function(newVector, startPos, size, opacity) {
			vector = newVector;
			position = startPos;
			updateAttributes(size, opacity);
			updatePosition();
			$('body').append($element);
			isOnscreen = true;
		};

		this.render = function(interval, bounds) {
			if (isOnscreen) {
				position[0] = position[0] + (interval * vector[0]);
				position[1] = position[1] + (interval * vector[1]);
				checkExpired(bounds);
				updatePosition();
			}
		};
	};


	var SnowFlakeEmitter = function(settings) {
		var flakes = [];
		var reclaimedFlakes = [];
		var lastTime = 0;

		var shouldSpawnNewFlake = function() {
			//console.log("size"+flakes.size);
			//return (flakes.size < 10);
			return (Math.random() * 100) < settings.intensity;
		};

		var getScreenBounds = function() {
			var rect = document.getElementById("main").getBoundingClientRect();
			//console.log(rect);
			return {
				//x: $(window).width(),
				//y: $(window).height(),
				x: rect.left,
				y: rect.top,
				x2: rect.right + 200,
				y2: rect.bottom + 2000
			};
		};

		var randomBetween = function(min,max) {
			return Math.random()*(max-min+1)+min;
		};

		var newFlakeVector = function() {
			var x = randomBetween(settings.driftRange[0], settings.driftRange[1]);
			var y = randomBetween(settings.speedRange[0], settings.speedRange[1]);
			return [x, y];
		};

		var newFlakePosition = function(bounds) {
			var x = randomBetween(bounds.x, bounds.x2); //had 20 on both sides
			var y = bounds.y; //-20
			return [x, y];
		};

		var reclaimFlake = function(flake) {
			reclaimedFlakes.push(flake);
		};

		var getFlake = function() {
			var flake;
			if (reclaimedFlakes.length > 50) {
				flake = reclaimedFlakes.shift(); //pop
				//console.log('oh no'+reclaimedFlakes.length);
			} else {
				flake = new SnowFlake(reclaimFlake);
				flakes.push(flake);
				//console.log('size: '+reclaimedFlakes.length)
			}
			return flake;
		};

		var spawnNewFlake = function(bounds) {
			var flake = getFlake();
			flake.spawn(
				newFlakeVector(),
				newFlakePosition(bounds),
				randomBetween(settings.sizeRange[0], settings.sizeRange[1]),
				randomBetween(settings.opacityRange[0], settings.opacityRange[1])
			);
		};

		var getInterval = function() {
			var time = Date.now();
			var interval = 0;

			if (lastTime) {
				interval = (time - lastTime) / 1000;
			}

			lastTime = time;
			return interval;
		};

		this.render = function() {
			var i, l = flakes.length;
			var interval = getInterval();
			var bounds = getScreenBounds();

			if (shouldSpawnNewFlake()) {
				spawnNewFlake(bounds);
			}

			for(i = 0; i < l; ++i) {
				flakes[i].render(interval, bounds);
			}
		};
	};

	// Create the defaults once
	var pluginName = "snow",
		defaults = {
			intensity: 10,
			sizeRange: [10, 20],
			opacityRange: [0.5, 1],
			driftRange: [-2, 2],
			speedRange: [25, 80]
		};

	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		// jQuery has an extend method which merges the contents of two or
		// more objects, storing the result in the first object. The first object
		// is generally empty as we don't want to alter the default options for
		// future instances of the plugin
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}

	// Avoid Plugin.prototype conflicts
	$.extend(Plugin.prototype, {
		init: function () {
			var snow = new SnowFlakeEmitter(this.settings);
			if (window.requestAnimationFrame) {
				function render() {
					snow.render();
					window.requestAnimationFrame(render);
				}
				window.requestAnimationFrame(render);
			} else {
				setInterval(function() {
					snow.render();
				}, 1/60);
			}
		}
	});

	$.fn[ pluginName ] = function ( options ) {
		this.each(function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
		return this;
	};

})( jQuery, window, document );


</script>

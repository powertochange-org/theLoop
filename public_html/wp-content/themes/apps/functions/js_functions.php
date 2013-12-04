<script type="text/javascript">

	//not tested for negative numbers
	function number2currency(v){
		v = v.toFixed(2).toString().split("").reverse();
		var c = "";
		for (var i = 0; i < v.length; i ++){
			if (i % 3 == 0 && i > 5){
				c = "," + c;
			}
			c = v[i] + c;
		}
		return "$" + c;
	}

	function get_value_float(element){
		var value = document.getElementById(element).value;
		if (value == ""){
			return 0;
		}
		if (isNaN(parseFloat(value))){
			//TODO throw exception?
			return 0;
		}
		return Math.max(parseFloat(value), 0);
	}
</script>
<style type="text/css">
	* {
		font-family: sans-serif;
	}
	h1 {
		font-family: sans-serif;
	    font-size: 22px;
	    padding: 5px 16px;
	    background: #ffc5c5;
	    display: inline-block;
	    vertical-align: top;
	    margin: 0;
	    color: #651515;
	    border-radius: 4px;
	}
	p {
		font-size: 18px;
    	color: #111;
	}
	span {
		font-size: 14px;
		color: #636363;
		padding: 4px;
		background: #e4e4e4;
		display: inline-block;
	}
	pre {
		font-size: 12px;
	}
</style>
<h1><?=$context['title']?></h1>
<p><?=$context['message']?> <br/> <span><?=$context['file']?> : <?=$context['line']?></span></p>
<pre><?=$context['trace']?></pre>
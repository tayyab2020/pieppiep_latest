<div class="alert-box"></div>

<style>

	.alert-box
	{
		position: absolute;
		bottom: 0;
		z-index: 10000;
	}

	.alert-simple
	{
		position: relative;
	}

	.alert-dismissible .close
	{
		top: 0;
		opacity: 1;
	}
    
	.alert>.start-icon {
    	margin-right: 0;
    	min-width: 20px;
    	text-align: center;
		font-style: normal;
	}

	.alert>.start-icon {
    	margin-right: 5px;
	}

	.cross
	{
  		font-size: 18px;
      	color: white;
    	text-shadow: none;
	}

	.alert-simple.alert-success
	{
  		border: 1px solid rgb(82 203 65 / 68%);
    	background-color: rgb(0 147 62);
    	box-shadow: 0px 0px 2px #259c08;
    	color: #dde5dd;
  		/* text-shadow: 2px 1px #00040a; */
  		transition:0.5s;
  		cursor:pointer;
	}

	.alert-simple.alert-success:hover{
  		background-color: rgb(7 149 66 / 85%);
  		transition:0.5s;
	}

	.alert-simple.alert-danger
	{
  		border: 1px solid rgba(241, 6, 6, 0.81);
    	background-color: rgb(220 17 1);
    	box-shadow: 0px 0px 2px #ff0303;
    	color: #dde5dd;
    	/* text-shadow: 2px 1px #00040a; */
  		transition:0.5s;
  		cursor:pointer;
	}

	.alert-simple.alert-danger:hover
	{
     	background-color: rgb(220 17 1 / 70%);
  		transition:0.5s;
	}

	.alert-simple:before {
    	content: '';
    	position: absolute;
    	width: 0;
    	height: calc(100% - 44px);
    	border-left: 1px solid;
    	border-right: 2px solid;
    	border-bottom-right-radius: 3px;
    	border-top-right-radius: 3px;
    	left: 0;
    	top: 50%;
    	transform: translate(0,-50%);
      	height: 20px;
	}

	.faa-tada.animated
	{
		-webkit-animation: tada 2s linear infinite;
		animation: tada 2s linear infinite;
	}

	.faa-pulse.animated
	{
		-webkit-animation: pulse 2s linear infinite;
		animation: pulse 2s linear infinite;
	}

	.fa-times
	{
		-webkit-animation: blink-1 2s infinite both;
	    animation: blink-1 2s infinite both;
	}


	/**
 	* ----------------------------------------
 	* animation blink-1
 	* ----------------------------------------
 	*/
	@-webkit-keyframes blink-1 {
  		0%,
  		50%,
  		100% {
    		opacity: 1;
  		}
  		25%,
  		75% {
    		opacity: 0;
  		}
	}
	
	@keyframes blink-1 {
  		0%,
  		50%,
  		100% {
    		opacity: 1;
  		}
  		25%,
  		75% {
    		opacity: 0;
  		}
	}

	@-webkit-keyframes tada {
  		0% {
    		-webkit-transform: scale(1);
			transform: scale(1);
  		}
  		10%, 20% {
			-webkit-transform: scale(.9) rotate(-8deg);
    		transform: scale(.9) rotate(-8deg);
  		}
		30%, 50%, 70% {
			-webkit-transform: scale(1.3) rotate(8deg);
			transform: scale(1.3) rotate(8deg);
		}
		40%, 60% {
			-webkit-transform: scale(1.3) rotate(-8deg);
			transform: scale(1.3) rotate(-8deg);
		}
		100%, 80% {
			-webkit-transform: scale(1) rotate(0);
			transform: scale(1) rotate(0);
		}
	}
	
	@keyframes tada {
  		0% {
    		-webkit-transform: scale(1);
			transform: scale(1);
  		}
  		10%, 20% {
			-webkit-transform: scale(.9) rotate(-8deg);
    		transform: scale(.9) rotate(-8deg);
  		}
		30%, 50%, 70% {
			-webkit-transform: scale(1.3) rotate(8deg);
			transform: scale(1.3) rotate(8deg);
		}
		40%, 60% {
			-webkit-transform: scale(1.3) rotate(-8deg);
			transform: scale(1.3) rotate(-8deg);
		}
		100%, 80% {
			-webkit-transform: scale(1) rotate(0);
			transform: scale(1) rotate(0);
		}
	}

	@-webkit-keyframes pulse {
  		0% {
    		-webkit-transform: scale(1.1);
			transform: scale(1.1);
  		}
  		50% {
			-webkit-transform: scale(.8);
    		transform: scale(.8);
  		}
		100% {
			-webkit-transform: scale(1.1);
			transform: scale(1.1);
		}
	}
	
	@keyframes pulse {
		0% {
    		-webkit-transform: scale(1.1);
			transform: scale(1.1);
  		}
  		50% {
			-webkit-transform: scale(.8);
    		transform: scale(.8);
  		}
		100% {
			-webkit-transform: scale(1.1);
			transform: scale(1.1);
		}
	}

</style>
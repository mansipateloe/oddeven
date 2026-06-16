// FUNCTION CODE
function gjCountAndRedirect(secounds, url)
{

		$('#gj-counter-num').text(secounds);

		$('#gj-counter-box').show();

	var interval = setInterval(function()
	{

		secounds = secounds - 1;

		$('#gj-counter-num').text(secounds);

		if(secounds == 0)
		{

			clearInterval(interval);
			window.location = url;
			$('#gj-counter-box').hide();
		
		}
		
	}, 1000);

	$('#gj-counter-box').click(function()
	{
		clearInterval(interval);
		window.location = url;
	
	});
}
// USE EXAMPLE
function startTime()
{
	var gjCountAndRedirectStatus = false; //prevent from seting multiple Interval
	if(gjCountAndRedirectStatus == false)
	{
		gjCountAndRedirect(900,'login.php');
		gjCountAndRedirectStatus = true;
	}
}

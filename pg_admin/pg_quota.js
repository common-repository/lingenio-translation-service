/*
calc quota for dashboard
*/

function progress(value)
{
    diag = document.getElementById("pg_quota").firstChild;

	if(value < 51)
		diag.style.background = 'r'+'gb(255, ' + Math.floor(value / 50 * 255) + ', 0)';	
	else
		diag.style.background = 'r'+'gb(' + Math.floor(255 - ((value - 50) / 50 * 255)) + ', 255, 0)';
		
	diag.firstChild.firstChild.nodeValue = diag.style.width = value + "%";
}
	
countUpP = 0;
perc = 100;
function countUp()
{
    if(countUpP == perc) return;
    progress(++countUpP);
    window.setTimeout("countUp()", 30);
}

//entry point
function calcQuota(stop)
{
    perc = stop;     
    countUp();    
}
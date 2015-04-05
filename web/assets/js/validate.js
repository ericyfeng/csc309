
		function validate()
		{//check to make sure the end date makes sense which means...
		 //	the end date MUST: come after the current date
		 //					   be on a real date (no april 31 or feb 29 in non leap years etc)
			var month = document.getElementById("month").value;
			var day = document.getElementById("day").value;
			var year = document.getElementById("year").value;
			var todayyear = new Date().getFullYear();
			var todaymonth = new Date().getMonth()+1;
			var todayday = new Date().getDate();
				
			//check to make sure the end date is after the current date
			if(year == todayyear)
			{
				if(month < todaymonth)
				{
					document.getElementById("warning").innerHTML="Project can't end before it starts";
					return false;
				}
				if(month == todaymonth)
				{
					if(day < todayday)
					{
						document.getElementById("warning").innerHTML="Project can't end before it starts";
						return false;
					}
					else if (day == todayday)
					{
						document.getElementById("warning").innerHTML="Project should last at least a day";
						return false;
					}
				}
			}
				
			//april, june, september, november only have 30 days so 31st in these months
			if(month==4 || month==6 || month==9 || month==11)
			{
				if(day == 31) 
				{	
					document.getElementById("warning").innerHTML="Date does not exist";
					return false;
				}
			}

			//february leap year checking
			if(month==2)
			{
				if((year%4 == 0) && (day > 29))
				{
					document.getElementById("warning").innerHTML="You only get up to 29 in "+year;
					return false;
				}
				else if(day>28) 
				{
					document.getElementById("warning").innerHTML="No Feb 29 in "+year;
					return false;
				}
			}
			document.getElementById("warning").innerHTML="it's ok";

			//if all the tests pass, the end date is ok
			return true;
		}

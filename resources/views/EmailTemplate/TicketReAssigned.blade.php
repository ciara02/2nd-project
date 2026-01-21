<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Inquiry</title>
			<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	 </head>

	 <body style="margin: 10px; padding: 30px; font-family:'Calibri'; font-weight: 300; line-height: 1.5em; text-align: left; background-color: #fff; border-radius:15px;">
			<h3 style="font-family:'Calibri';font-size: 20px" >Good day {{ $assignRecord->assign_name ?? 'Assignee' }}, </h3> 
		<p style="font-family:'Calibri';font-size: 16px"> 
			We have reassigned this ticket [<strong>{{ $ticket->ticket_number }}</strong>] to  another engineer.
        <table align="center" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff; margin: 10px 0; padding: 50px; border-radius: 15px; ">
			<tr>
				 <td  style="font-family:'Calibri'; ">
				 <div class="alert alert-info" role="alert" style="border:2px solid #000c0c;color: #3a603d; padding: 10px; font-size: 14px;  border-radius: 15px;">
				 <p><strong>Company Name:</strong>{{ $ticket->company_name }} </p>
				  <p><strong>Company Email:</strong>{{ $ticket->cotact_email }} </p>
					<p><strong>Concern:</strong> {{ $ticket->concern }} </p>
					 @if(!empty($departments))
                      @foreach ($departments as $dept)
                      <p>Contract: {{ $dept->prod_name }}</p>
                      @endforeach
                       @endif
					 <p>
                     Date Reported:
                    {{ $ticket->date_created ? \Carbon\Carbon::parse($ticket->date_created)->format('M-d-Y h:i A') : 'N/A' }}
                     </p>
				 </div>

				 </td>
			 </tr><p style="font-family:'Calibri';font-size: 16px">If you have any concerns and inquries please call <strong>8329972-74 </strong>  or You may email the team( <strong>techsupport@msi-ecs.com.ph</strong> ) </p>
	 </table>

	 </body>

</html>
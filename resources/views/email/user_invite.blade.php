<div style="width:700px; padding: 10px; background-color:#E5E5E6">
<div style="padding:20px 50px; margin:-10px -10px 10px -10px; background-color: #CCCCCC">
	<a href="#><img src="{{asset('img/logo.png')}}" alt="FindaLoo"/></a>
</div>

<div style="text-align:center;padding: 20px 50px; background-color: #FFFFFF ; font-family: Verdana, Geneva, sans-serif;font-size:14px;">
	<div class="content">
      <table>
        <tr>
          <td style="text-align: justify;">
            <p>Dear {{$name}},</p>
            <h3>Greetings from FindaLoo!!</h3>
            <p>Welcome and Thank you!!!<br/> You have successfully created your account.</p>
            <p>Your next step is activating your account, which is an integral part of the registration process; instructions below provide step-by-step guidance to help you to complete the team registration.</p>

            <h4>Here are your login credentials:</h4>
            <table>
            	<tr>
            		<td>Username</td>
            		<td>:</td>
            		<td><b>{{$email}}</b></td>
            	</tr>
            	<tr>
            		<td>Password</td>
            		<td>:</td>
            		<td><b>{{$password}}</b></td>
            	</tr>
            </table>
            <p><b>Click the button below to activate your account</b></p>
            <!-- button -->
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <a href="{{ URL::to('/verifyAccount/'.$token) }}" style="background-color: #337ab7;border-color: #2e6da4;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;color:white;text-decoration:none;border: 1px solid transparent;border-radius: 4px;color: #fff;margin-top: 5px;margin-bottom: 5px;padding: 6px 12px;"  target="_blank">Activate</a>
                </td>
              </tr>
            </table>
            <!-- /button -->
          </td>
        </tr>
      </table>
     </div>
</div>
<div style="padding:20px 50px; margin:10px -10px -10px -10px; background-color: #999999">
	<p align="center">Team FindaLoo</p>
</div>
</div>
<div style="width:700px; padding: 10px; background-color:#E5E5E6">
<div style="padding:20px 50px; margin:-10px -10px 10px -10px; background-color: #CCCCCC">
	<a href="www.findaloo.org"><img src="{{asset('img/logo.png')}}" alt="FindaLoo"/></a>
</div>

<div style="text-align:center;padding: 20px 50px; background-color: #FFFFFF ; font-family: Verdana, Geneva, sans-serif;font-size:14px;">
	<div class="content">
      <table>
        <tr>
          <td style="text-align: justify;">
            <p>Dear {{$username}},</p>
            <h3>Greetings from FindaLoo!!</h3>
            <p>Recently you tried to reset your password using our app.</p>

            <p><b>Click the below button to reset your password.</b></p>
            <!-- button -->
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <a href="{{ URL::to('auth/verify_pass/'.$username.'/'.$token) }}" style="background-color: #337ab7;border-color: #2e6da4;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;color:white;text-decoration:none;border: 1px solid transparent;border-radius: 4px;color: #fff;margin-top: 5px;margin-bottom: 5px;padding: 6px 12px;"  target="_blank">Reset password</a>
                </td>
              </tr>
            </table>
            <!-- /button -->
            <p>For any queries, please contact us at <b>admin@e-yantra.org</b> .<p>
            <p>
            	Best wishes,<br/>
                Team FindaLoo
            </p>
          </td>
        </tr>
      </table>
     </div>
</div>
<div style="padding:20px 50px; margin:10px -10px -10px -10px; background-color: #999999">
	<p align="center">FindaLoo</p>
</div>
</div>
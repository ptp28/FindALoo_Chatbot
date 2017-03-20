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
            <p>Recently you tried to reset your password using our app. New password has been generated for you.</p>

            <h4>Here are your login credentials:</h4>
            <table>
              <tr>
                <td>Username</td>
                <td>:</td>
                <td><b>{{$username}}</b></td>
              </tr>
              <tr>
                <td>New Password</td>
                <td>:</td>
                <td><b>{{$newpassword}}</b></td>
              </tr>
            </table>
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
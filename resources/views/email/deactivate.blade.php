<div style="width: 700px; padding: 10px; background-color: #e5e5e6;">
    <div style="padding: 20px 50px; margin: -10px -10px 10px -10px; background-color: #cccccc;"><a href="&quot;#"><img src="{{asset('img/logo.png')}}" alt="FindaLoo" /></a></div>
    <div style="text-align: center; padding: 20px 50px; background-color: #ffffff; font-family: Verdana, Geneva, sans-serif; font-size: 14px;">
        <div class="content">
            <table>
                <tbody>
                    <tr>

                        <td style="text-align: justify;">
                            <p>Dear Admin,</p>
                            <h3>Greetings from FindaLoo!!</h3>
                            <p>This is to inform you that a toilet has been marked as inactive by the user, kindly verify and approve by clicking the button below. Following are the details :</p>
                            
                            <p>User details :&nbsp;</p>
                              <table>
                                <tr>
                                  <td>Name</td>
                                  <td>:</td>
                                  <td><b>{{$user->name}}</b></td>
                                </tr>
                                <tr>
                                  <td>Email</td>
                                  <td>:</td>
                                  <td><b>{{$user->email}}</b></td>
                                </tr>
                              </table>

                            <p>Toilet details :&nbsp;</p>
                              <table>
                                <tr>
                                  <td>Toilet Name</td>
                                  <td>:</td>
                                  <td><b>{{$toilet->NAME}}</b></td>
                                </tr>
                                <tr>
                                  <td>Location</td>
                                  <td>:</td>
                                  <td><a href="http://maps.google.com/?q={{$toilet->lat}},{{$toilet->lng}}">Here</a></td>
                                </tr>
                              </table>
                            <p><strong>Click the button below to Deactivate the specified toilet after verification.</strong></p>
                            <!-- button -->
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tbody>
                                    <tr>
                                        <td><a style="background-color: #337ab7; border-color: #2e6da4; font-size: 14px; font-weight: 400; line-height: 1.42857143; text-align: center; color: #fff; text-decoration: none; border: 1px solid transparent; border-radius: 4px; margin-top: 5px; margin-bottom: 5px; padding: 6px 12px;" href="{{ URL::to('/updateToilet/'.$token.'/'.$toilet->OBJECTID.'/'.$active) }}" target="_blank">DeActivate</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- /button -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div style="padding: 20px 50px; margin: 10px -10px -10px -10px; background-color: #999999;">
        <p align="center">Team FindaLoo</p>
    </div>
</div>
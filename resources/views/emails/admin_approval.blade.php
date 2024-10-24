<table style="text-align:left;width: 100%;  max-width:500px;background-color:#f8f8f8;border-radius:3px;font-family:helvetica;" cellpadding="10">
    <tr>
      <td colspan="2" style="text-align:center;font-weight: bold; font-size: 20px; color: #E12E47;padding-top:30px;" align="center">
        {{ $data['message'] }}
      </td>
    </tr>


     <tr>
       <th width='150px'>Message : </th>
       <td>{{ $data['message'] }}</td>
     </tr>
     @if($data['login'])
     <tr>
        <th width='150px'>Link : </th>
        <td>
            <a href="https://speakmydialect.com.au/login"> click here to login </a>
            </td>
      </tr>
      @endif



  </table>

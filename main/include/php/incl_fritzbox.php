<?
/*
*/
?><? function fritzbox_Call($phoneNumber, $delay)
{
    if (!global_phoneGatewayActive) {
        return false;
    }
    try {
        $cl = new SoapClient(null, array('location' => "http://" . global_fbIp . ":" . global_fbSoapPort . "/upnp/control/x_voip", 'uri' => "urn:dslforum-org:service:X_VoIP:1", 'login' => global_fbLogin, 'password' => global_fbPassword));
        $r = $cl->{"X_AVM-DE_DialNumber"}(new SoapParam($phoneNumber, "NewX_AVM-DE_PhoneNumber"));
        if (!is_soap_fault($r)) {
            if ($delay > 0) {
                sleep($delay);
                fritzbox_HangUp();
            }
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function fritzbox_HangUp()
{
    if (!global_phoneGatewayActive) {
        return false;
    }
    try {
        $cl = new SoapClient(null, array('location' => "http://" . global_fbIp . ":" . global_fbSoapPort . "/upnp/control/x_voip", 'uri' => "urn:dslforum-org:service:X_VoIP:1", 'login' => global_fbLogin, 'password' => global_fbPassword));
        $r = $cl->{"X_AVM-DE_DialHangup"}();
        if (!is_soap_fault($r)) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function fritzbox_GetWanIP()
{
    if (!global_phoneGatewayActive) {
        return false;
    }
    try {
        $cl = new SoapClient(null, array('location' => "http://" . global_fbIp . ":" . global_fbSoapPort . "/upnp/control/wanipconnection1", 'uri' => "urn:dslforum-org:service:WANIPConnection:1", 'login' => global_fbLogin, 'password' => global_fbPassword));
        $r = $cl->GetExternalIPAddress();
        if (!is_soap_fault($r)) {
            if (filter_var($r, FILTER_VALIDATE_IP) !== false) {
                return $r;
            } else {
                return false;
            }
        }
    } catch (Exception $e) {
        return false;
    }
} ?>

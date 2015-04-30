<?php
namespace Oda;
use stdClass;
/**
 * LIBODA Librairy - main class
 *
 * Tool
 *
 * @author  Fabrice Rosito <rosito.fabrice@gmail.com>
 * @version 0.150127
 */
class OdaLib {
    /**
     * class constructor
     *
     * @param stdClass $p_params
     * @return OdaDate $this
     */
    public function __construct($p_params = NULL){
        
    }
    /**
     * Destructor
     *
     * @access public
     * @return null
     */
    public function __destruct(){
        
    }
    /**
    * @name fomatage_json
    * @param class $p_object_retour
    * @return string
    */
    static function fomatage_json($p_object) {
       try {
           $output = "";
           //$object_retour est traduit dans son ensemble
           if(isset($p_object)) {
               $resultats_json = json_encode($p_object);
               $resultats_json = str_replace("\/", "/", $resultats_json);
               $output = $resultats_json;
           }

           return $output;
       } catch (\Exception $e) {
           $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
       }
    }
    /**
     * @name fomatage_text
     * @param string $p_strSorti
     * @return string
     */
    static function fomatage_text($p_strSorti) {
        try {
            //$strSorti à valeur d'origine
            return $p_strSorti;
        } catch (\Exception $e) {
           $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
        }
    }
    /**
     * @name fomatage_xml
     * @param class $p_object_retour
     * @return string
     */
    static function fomatage_xml($p_object) {
        try {
            $output = "";
            //$object_retour->data["resultat"]->data est traduit en xml, c'est un tableau en théorie
            if(isset($p_object)) {
                if(is_array($p_object)){
                    $output = self::generate_valid_xml_from_array($p_object);
                }
            }
            return $output;
        } catch (\Exception $e) {
           $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
        }
    }
    /**
     * @name fomatage_csv
     * @param class $p_object_retour
     * @return string
     */
    static function fomatage_csv($p_object) {
        try {
            $output = "";
            //$object_retour->data["resultat"]->data est traduit en xml, cela doit être un tableau
            if(isset($p_object)) {
                if(is_array($p_object)){
                    $output = self::arrayToCsv($p_object);
                }
            }
            return $output;
        } catch (\Exception $e) {
           $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
        }
    }
    /**
     * @name generate_valid_xml_from_array
     * @param array $array
     * @param string $node_block
     * @param string $node_name
     * @return string
     */
    static function generate_valid_xml_from_array($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";

        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= self::generate_xml_from_array($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";

        return $xml;
    }
    /**
     * @name generate_xml_from_array
     * @param array $array
     * @param string $node_name
     * @return string
     */
    static function generate_xml_from_array($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                        $key = $node_name;
                }

                $xml .= '<' . $key . '>' . self::generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
            }
        } else {
            $xml =  htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }
    /**
     * @name arrayToCsv
     * @param array $array
     * @return string
     */
    static function arrayToCsv($array) {
        $csv = '';
        if ((is_array($array) || is_object($array))&&(isset($array[0]))) {
            //entête
            foreach ($array[0] as $labelCol=>$value){
                if (is_numeric($labelCol)) {
                    $csv .= $labelCol .';';
                }else{
                    $val = htmlspecialchars($labelCol, ENT_QUOTES);
                    $csv .= '"'.$val .'";';
                }
            }
            $csv .= "\n";
            //data
            foreach($array as $key => $val) {
                foreach ($array[$key] as $labelCol=>$value) {
                    if (is_numeric($value)) {
                        $csv .= $value .';';
                    }else{
                        $val = htmlspecialchars($value, ENT_QUOTES);
                        $csv .= '"'.$val .'";';
                    }
                }
                $csv .= "\n";
            }
        }

        return $csv;
    } 
    /**
     * @name sendMailGun
     * @p_param sdtClass $p_params
     */
    static function sendMailGun($p_params) {
        try {
            $config = OdaConfig::getInstance();
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$config->MAILGUN->api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v2/'.$config->MAILGUN->domaine.'/messages');

            $params = array(
                'from' => $p_params["email_labelle_ori"].'<'.$p_params["email_mail_ori"].'>'
                ,'to' => $p_params["email_mails_dest"]
                ,'subject' => $p_params["sujet"]
                ,'html' => $p_params["message_html"]
                ,'text' => $p_params["message_txt"]
            );

            if(!is_null($p_params["email_mails_copy"])){
                $params['cc'] = $p_params["email_mails_copy"];
            }

            if(!is_null($p_params["email_mails_cache"])){
                $params['bcc'] = $p_params["email_mails_cache"];
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $result = curl_exec($ch);
            curl_close($ch);
            
            $return = ($result)?"OK":"KO";

            return $return;
        } catch (Exception $e) {
            $msg = $e->getMessage();
           Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
           return null;
       }
    }
    /**
     * @name sendMail
     * @p_param sdtClass $p_params
     */
    static function sendMail($p_params) {
        try {
            $strRetour = "ok";
            
            if (ini_get("SMTP") == "none") {
                $strRetour = "ko: smtp in localhost";
                goto gotoFinish;
            }

            if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $p_params["email_mails_dest"])){
                $passage_ligne = "\r\n";
            }else{
                $passage_ligne = "\n";
            }

            //=====Création de la boundary
            $boundary = "-----=".md5(rand());
            //==========

            //=====Création du header de l'e-mail.
            if(($p_params["email_mail_ori"] != null)&&($p_params["email_labelle_ori"] != null)){
                $header = "From: \"".$p_params["email_labelle_ori"]."\"<".$p_params["email_mail_ori"].">".$passage_ligne;
            }
            if(($p_params["email_mail_reply"] != null)&&($p_params["email_labelle_reply"] != null)){
                $header.= "Reply-to: \"".$p_params["email_labelle_reply"]."\" <".$p_params["email_mail_reply"].">".$passage_ligne;
            }
            if($p_params["email_mails_copy"] != null){
                $header.= "Cc: ".$p_params["email_mails_copy"]."".$passage_ligne;
            }
            if($p_params["email_mails_cache"] != null){
                $header.= "Bcc: ".$p_params["email_mails_cache"]."".$passage_ligne;
            }
            $header.= "MIME-Version: 1.0".$passage_ligne;
            $header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
            //==========

            //=====Création du message.
            $message = $passage_ligne."--".$boundary.$passage_ligne;
            //=====Ajout du message au format texte.
            $message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
            $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
            $message.= $passage_ligne.$p_params["message_txt"].$passage_ligne;
            //==========
            $message.= $passage_ligne."--".$boundary.$passage_ligne;
            //=====Ajout du message au format HTML
            $message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
            $message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
            $message.= $passage_ligne.$p_params["message_html"].$passage_ligne;
            //==========
            $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
            $message.= $passage_ligne."--".$boundary."--".$passage_ligne;
            //==========

            //=====Envoi de l'e-mail.
            if(!mail($p_params["email_mails_dest"],$p_params["sujet"],$message,$header)){
              $strRetour = "KO";
            }
            //==========
            gotoFinish:
            return $strRetour;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
            return null;
       }
    }
    
    /**
    * test
    * @global stdClass $bufferTest
    * @param type $name
    * @param type $anonFunc
    * @return \stdClass
    */
    static function test($name, $anonFunc) {
        global $bufferTest;
        $bufferTest = new stdClass();
        $retoursTest = array();
        $bufferTest->name = $name;
        $bufferTest->retour = $retoursTest;
        $anonFunc(); 

        $bufferTest->succes = 0;
        $bufferTest->echec = 0;
        $bufferTest->total = 0;
        foreach($bufferTest->retour as $key => $value) {
             if ($value->statut == "OK"){
                $bufferTest->succes += 1;
                $bufferTest->total += 1;
             }else{
                $bufferTest->echec += 1;
                $bufferTest->total += 1;
             }
         }

        return $bufferTest;
    }
    
    /**
     * get_string_between
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     */
    static function get_string_between($string, $start, $end){
        try {
            $string = " ".$string;
            $ini = strpos($string,$start);
            if ($ini == 0) return "Start non trouve";
            $ini += strlen($start);
            $len = strpos($string,$end,$ini) - $ini;
            return substr($string,$ini,$len);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
            return null;
        }
    }

    /**
     * equal
     * @global stdClass $bufferTest
     * @param type $param1
     * @param type $param2
     * @param type $message
     */
    static function equal($param1, $param2, $message) {
        global $bufferTest;
        $object_retour = new stdClass();
        try{
            if($param1 == $param2){
                $object_retour->statut = "OK";
                $object_retour->message = $message;
            }else{
                $object_retour->statut = "KO";
                $object_retour->message = $message;
                $object_retour->attendu = $param2;
                $object_retour->resultat = $param1;
                $object_retour->erreur = "";
            }
            $bufferTest->retour[] = $object_retour;
        }catch ( Exception $e ) {
            $object_retour->statut = "KO";
            $object_retour->message = $message;
            $object_retour->attendu = $param2;
            $object_retour->resultat = $param1;
            $object_retour->erreur = $e->getMessage();
            $bufferTest->retour[] = $object_retour;
        }
    }
        
    /**
     * class CallRest
     * @access public
     * @param type $method : POST, PUT, GET etc
     * @param type $url
     * @param type $data : array("param" => "value") ==> index.php?param=value
     * @return type
     */
    static function CallRest($p_url, $p_params = null, $p_data = null){
        $result = null;
        $url = $p_url;

        $params_attempt = new stdClass();
        $params_attempt->method = 'GET';
        $params_attempt->dataTypeRest = 'json';
        $params_attempt->debug = false;

        try {
            $params = (object) array_merge((array) $params_attempt, (array) $p_params);

            if($params->debug){
                echo('$p_url :');
                var_dump($p_url);
                echo('$params :');
                var_dump($params);
                echo('$p_data :');
                var_dump($p_data);
            }

            $curl = curl_init();

            switch ($params->method)
            {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);

                    if ($p_data != null)
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $p_data);
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
                    if ($p_data != null)
                        $url = sprintf("%s?%s", $url, http_build_query($p_data));
            }

            // Optional Authentication:
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, "username:password");

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $retourCall = curl_exec($curl);

            curl_close($curl);

            switch ($params->dataTypeRest)
            {
                case "json":
                    $result = json_decode($retourCall);
                    break;
                default:
                    $result = $retourCall;
            }

            if($result == null){
                $result = $retourCall;
            }

            if($params->debug){
                echo('$result :');
                var_dump($result);
            }

            return $result;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            Throw new \Exception('Erreur dans '.__CLASS__.' : '.$msg);
            return null;
        }
    }
}
<?
class main {
    private $prefix;


    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    public function zapisz() {
        $postData = $_POST;

        $z1 = $this->zapiszZalacznik($_FILES['file_dowod_rejestracyjny']);
        $z2 = $this->zapiszZalacznik($_FILES['file_ubezpieczenie']);
        $z3 = $this->zapiszZalacznik($_FILES['file_dowod']);
        $z4 = $this->zapiszZalacznik($_FILES['file_dowod_rejestracyjny_2']);
        $z5 = $this->zapiszZalacznik($_FILES['file_umowa_kupna']);
        $zalaczniki = array_merge_recursive($z1, $z2, $z3, $z4, $z5);

        $this->wyslijFormularz($zalaczniki);
    }

    private function zapiszZalacznik($plik) {
        if (is_uploaded_file($plik['tmp_name'])) {
            $dir = 'uploaded/';
            $target_file = $dir . basename($plik['name']);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            move_uploaded_file($plik['tmp_name'], $target_file);
        }
        return array(
            'zalacznik' => $target_file,
            'zalacznikNazwa' => basename($plik['name'])
        );
    }

    private function wyslijFormularz($zalaczniki = false) {
        $row = $this->stworzDaneEmaila(); // Tutaj stworzenie danych do wiadomości email

        $this->sendEmail('NOWE ZAPYTANIE O UBEZPIECZENIE DOMU', $row, 'email_formularz.php', 'kuppolise24@gmail.com', $zalaczniki['zalacznik'], $zalaczniki['zalacznikNazwa']);
    }

    private function stworzDaneEmaila() {
    	$dataArray = array();

    $fields = array(
        'imie_nazwisko_ubezpieczyciel' => 'imie_nazwisko_ubezpieczyciel',
    'pesel_ubezpieczyciel' => 'pesel_ubezpieczyciel',
    'telefon_ubezpieczyciel' => 'telefon_ubezpieczyciel',
    'email_ubezpieczyciel' => 'email_ubezpieczyciel',
    'ulica_ubezpieczyciel' => 'ulica_ubezpieczyciel',
    'nr_domu_ubezpieczyciel' => 'nr_domu_ubezpieczyciel',
    'postal_code_ubezpieczyciel' => 'postal_code_ubezpieczyciel',
    'miasto_ubezpieczyciel' => 'miasto_ubezpieczyciel',
    'czy_wspolwlasciciel' => 'czy_wspolwlasciciel',
    'imie_nazwisko_wspolwlasciciel' => 'imie_nazwisko_wspolwlasciciel',
    'pesel_wspolwlasciciel' => 'pesel_wspolwlasciciel',
    'ulica_wspolwlasciciel' => 'ulica_wspolwlasciciel',
    'nr_domu_wspolwlasciciel' => 'nr_domu_wspolwlasciciel',
    'postal_code_wspolwlasciciel' => 'postal_code_wspolwlasciciel',
    'miasto_wspolwlascicie__l' => 'miasto_wspolwlasciciel',
    'ulica_dom' => 'ulica_dom',
    'nr_domu_dom' => 'nr_domu_dom',
    'postal_code_dom' => 'postal_code_dom',
    'miasto_dom' => 'miasto_dom',
    'typ_ubezpieczenia' => 'typ_ubezpieczenia',
    'typ_zabudowy' => 'typ_zabudowy',
    'rok_budowy' => 'rok_budowy',
    'powierzchnia_uzytkowa' => 'powierzchnia_uzytkowa',
    'liczba_pieter' => 'liczba_pieter',
	'czy_zabezpieczenia' => 'czy_zabezpieczenia',
    'drzwi_antywlamaniowe' => 'drzwi_antywlamaniowe',
    'alarm' => 'alarm',
    'monitoring' => 'monitoring',
    'staly_dozor' => 'staly_dozor',
    'konstrukcja_budynku' => 'konstrukcja_budynku',
    'konstrukcja_dachu' => 'konstrukcja_dachu',
    'material_dachu' => 'material_dachu',
    'czy_remont' => 'czy_remont',
    'co_wymienione' => 'co_wymienione',
    'czy_ciaglosc' => 'czy_ciaglosc',
    'ile_lat_ubezpieczenia' => 'ile_lat_ubezpieczenia',
    'czy_cesja' => 'czy_cesja',
    'bank_data' => 'bank_data',
    'czy_wynajem' => 'czy_wynajem',
    'czy_powodz' => 'czy_powodz',
    'przerwa_zamieszkania' => 'przerwa_zamieszkania',
    'czy_okna_dachowe' => 'czy_okna_dachowe',
    'czy_piwnica' => 'czy_piwnica',
    'wartosc_budynku' => 'wartosc_budynku',
    'wartosc_ruchomosci' => 'wartosc_ruchomosci',
    'wartosc_instalacji' => 'wartosc_instalacji',
    'wartosc_garazu_wolnostojacego' => 'wartosc_garazu_wolnostojacego',
    'przepiecia' => 'przepiecia',
    'powodz' => 'powodz',
    'szyby' => 'szyby',
    'oc' => 'oc',
    'assistance' => 'assistance',
    'koszty_poszukiwania' => 'koszty_poszukiwania',
    'szkody_mrozowe' => 'szkody_mrozowe',
    'razace_niedbalstwo' => 'razace_niedbalstwo',
    'nww_mieszkancow' => 'nww_mieszkancow',
    'liczba_osob' => 'liczba_osob',
    'uwagi' => 'uwagi',
    );

    foreach ($fields as $fieldKey => $fieldName) {
        $dataArray[$fieldKey] = isset($this->vars[$fieldName]) ? $this->vars[$fieldName] : '';
    }
        return $dataArray;
    }


	 private function sendEmail($subject, $dataArray, $tpl_file, $email, $adresZalacznika = false, $nazwaZalacznika = false, $typZalacznika = false) {
        require_once('includes/class.tpl.email.php');
        $emailer = new email_class();

        if (is_array($dataArray)) {
            $arrayKeys = array_keys($dataArray);

            $TPLarray = array();
            $TPLarray['siteurl'] = $this->mainConfig->siteurl;
            $TPLarray['logo'] = $this->mainConfig->siteurl . '/theme/images/logo.png';
            $TPLarray['sitename'] = $this->mainConfig->sitename;
            $TPLarray['rok'] = date('Y');

            for ($i = 0; $i < count($arrayKeys); $i++) {
                $TPLarray[strtoupper($arrayKeys[$i])] = $this->formatSQL($dataArray[$arrayKeys[$i]]);
            }
            $emailer->assign_vars($TPLarray);

            $nadawca = false; // Określ nadawcę
            $nadawca_nazwa = false; // Określ nazwę nadawcy

            $emailer->email_sender($email, $tpl_file, $subject, $nadawca, $nadawca_nazwa, $adresZalacznika, $nazwaZalacznika, $typZalacznika);
        }
    }

    private function formatSQL($zmienna, $rodzaj = 'text') {
        $zmienna = htmlspecialchars(strip_tags($zmienna));

        switch ($rodzaj) {
            case 'numeric':
                return str_replace(' ', '', str_replace(',', '.', $zmienna));
                break;
            case 'text':
                return strip_tags(trim($zmienna));
                break;
        }
    }

    function wyslij_email($tytul, $wiadomosc, $email, $nadawcaEmail = false, $nadawcaNazwa = false, $adresZalacznika = false, $nazwaZalacznika = false, $typZalacznika = false)
    {
        date_default_timezone_set('Europe/Warsaw');

        require_once 'includes/PHPMailerAutoload.php';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = '';
        $mail->Port = ;
        $mail->SMTPAuth = true;
        // $mail->SMTPSecure = "tls";
        $mail->Username = '';
        $mail->Password = '';

        // $from = (!empty($nadawcaEmail)) ? $nadawcaEmail : $this->mainConfig->poczta_email'];
        // $fromName = (!empty($nadawcaNazwa)) ? $nadawcaNazwa : $this->mainConfig->poczta_nazwa'];
        $from = '';
        $fromName = '';

        $mail->setFrom($from, $fromName);
        if (!empty($nadawcaEmail)) {
            $mail->addReplyTo($nadawcaEmail, $nadawcaNazwa);
        }
        $mail->addAddress($email);
        $mail->Subject = $tytul;
        $mail->msgHTML($wiadomosc);
        // $mail->AltBody = 'This is a plain-text message body'; // Alternatywna wiadomość dla niewyświetlającej się głównej

        // Dodawanie załącznika
        switch ($typZalacznika) {
            case 'content':
                if ($typZalacznika == 'content' && !empty($adresZalacznika)) {
                    $mail->addStringAttachment($adresZalacznika, $nazwaZalacznika);
                }
                break;
            default:
                if (is_array($adresZalacznika)) {
                    for ($i = 0; $i < count($adresZalacznika); $i++) {
                        if (!empty($adresZalacznika[$i]) && !empty($nazwaZalacznika[$i])) {
                            $mail->addAttachment($adresZalacznika[$i], $nazwaZalacznika[$i]);
                        }
                    }
                } else if (!empty($adresZalacznika) && !empty($nazwaZalacznika)) {
                    $mail->addAttachment($adresZalacznika, $nazwaZalacznika);
                }
                break;
        }

        // Wyślij wiadomość, sprawdź czy wystąpiły błędy
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            exit;
        }
    }
?>

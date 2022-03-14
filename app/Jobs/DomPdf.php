<?php

namespace App\Jobs;

//use App\Http\Controllers\DomPdf;
use Barryvdh\DomPDF\PDF;

class DomPdf extends PDF
{

    /**
     * A dictionary of names and corresponding destinations (Dests key on document Catalog).
     * @protected
     * @since 5.9.097 (2011-06-23)
     */
    protected $dests = array();

    /**
     * Buffer holding in-memory PDF.
     * @protected
     */
    protected $buffer;

    /**
     * Boolean flag to enable document digital signature.
     * @protected
     * @since 4.6.005 (2009-04-24)
     */
    protected $sign = false;

    /**
     * ByteRange placemark used during digital signature process.
     * @since 4.6.028 (2009-08-25)
     * @public static
     */
    public static $byterange_string = '/ByteRange[0 ********** ********** **********]';

    /**
     * Digital signature data.
     * @protected
     * @since 4.6.005 (2009-04-24)
     */
    protected $signature_data = array();


    public function test()
    {
        echo "tesASDFASDFt";
    }

    public function downloadMe($name='doc.pdf', $dest='I')
    {
        //Output PDF to some destination
        //Finish document if necessary
//        if ($this->state < 3) {
//            $this->Close();
//        }
        //Normalize parameters
        if (is_bool($dest)) {
            $dest = $dest ? 'D' : 'F';
        }
        $dest = strtoupper($dest);
        if ($dest[0] != 'F') {
            $name = preg_replace('/[\s]+/', '_', $name);
            $name = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $name);
        }
        if ($this->sign) {
            // *** apply digital signature to the document ***
            // get the document content
            $pdfdoc = $this->getBuffer();
            // remove last newline
            $pdfdoc = substr($pdfdoc, 0, -1);
            // remove filler space
            $byterange_string_len = strlen(a::$byterange_string);
            // define the ByteRange
            $byte_range = array();
            $byte_range[0] = 0;
            $byte_range[1] = strpos($pdfdoc, DomPdf::$byterange_string) + $byterange_string_len + 10;
            $byte_range[2] = $byte_range[1] + $this->signature_max_length + 2;
            $byte_range[3] = strlen($pdfdoc) - $byte_range[2];
            $pdfdoc = substr($pdfdoc, 0, $byte_range[1]).substr($pdfdoc, $byte_range[2]);
            // replace the ByteRange
            $byterange = sprintf('/ByteRange[0 %u %u %u]', $byte_range[1], $byte_range[2], $byte_range[3]);
            $byterange .= str_repeat(' ', ($byterange_string_len - strlen($byterange)));
            $pdfdoc = str_replace(DomPdf::$byterange_string, $byterange, $pdfdoc);
            // write the document to a temporary folder
            $tempdoc = DomPdf::getObjFilename('doc', $this->file_id);
            $f = DomPdf::fopenLocal($tempdoc, 'wb');
            if (!$f) {
                $this->Error('Unable to create temporary file: '.$tempdoc);
            }
            $pdfdoc_length = strlen($pdfdoc);
            fwrite($f, $pdfdoc, $pdfdoc_length);
            fclose($f);
            // get digital signature via openssl library
            $tempsign = DomPdf::getObjFilename('sig', $this->file_id);
            if (empty($this->signature_data['extracerts'])) {
                openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED);
            } else {
                openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED, $this->signature_data['extracerts']);
            }
            // read signature
            $signature = file_get_contents($tempsign);
            // extract signature
            $signature = substr($signature, $pdfdoc_length);
            $signature = substr($signature, (strpos($signature, "%%EOF\n\n------") + 13));
            $tmparr = explode("\n\n", $signature);
            $signature = $tmparr[1];
            // decode signature
            $signature = base64_decode(trim($signature));
            // add TSA timestamp to signature
            $signature = $this->applyTSA($signature);
            // convert signature to hex
            $signature = current(unpack('H*', $signature));
            $signature = str_pad($signature, $this->signature_max_length, '0');
            // Add signature to the document
            $this->buffer = substr($pdfdoc, 0, $byte_range[1]).'<'.$signature.'>'.substr($pdfdoc, $byte_range[1]);
            $this->bufferlen = strlen($this->buffer);
        }
        switch($dest) {
            case 'I': {
                // Send PDF to the standard output
                if (ob_get_contents()) {
                    $this->Error('Some data has already been output, can\'t send PDF file');
                }
                if (php_sapi_name() != 'cli') {
                    // send output to a browser
                    header('Content-Type: application/pdf');
                    if (headers_sent()) {
                        $this->Error('Some data has already been output to browser, can\'t send PDF file');
                    }
                    header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
                    //header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                    header('Content-Disposition: inline; filename="'.basename($name).'"');
                    DomPdf::sendOutputData($this->getBuffer(), $this->bufferlen);
                } else {
                    echo $this->getBuffer();
                }
                break;
            }
            case 'D': {
                // download PDF as file
                $this->downloadPDFAsFile($name);
                DomPdf::sendOutputData($this->getBuffer(), $this->bufferlen);
                break;
            }
            case 'F':
            case 'FI':
            case 'FD': {
                // save PDF to a local file
                $f = DomPdf::fopenLocal($name, 'wb');
                if (!$f) {
                    $this->Error('Unable to create output file: '.$name);
                }
                fwrite($f, $this->getBuffer(), $this->bufferlen);
                fclose($f);
                if ($dest == 'FI') {
                    // send headers to browser
                    header('Content-Type: application/pdf');
                    header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
                    //header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
                    header('Content-Disposition: inline; filename="'.basename($name).'"');
                    DomPdf::sendOutputData(file_get_contents($name), filesize($name));
                } elseif ($dest == 'FD') {
                    // send headers to browser
                    $this->downloadPDFAsFile($name);
                    DomPdf::sendOutputData(file_get_contents($name), filesize($name));
                }
                break;
            }
            case 'E': {
                // return PDF as base64 mime multi-part email attachment (RFC 2045)
                $retval = 'Content-Type: application/pdf;'."\r\n";
                $retval .= ' name="'.$name.'"'."\r\n";
                $retval .= 'Content-Transfer-Encoding: base64'."\r\n";
                $retval .= 'Content-Disposition: attachment;'."\r\n";
                $retval .= ' filename="'.$name.'"'."\r\n\r\n";
                $retval .= chunk_split(base64_encode($this->getBuffer()), 76, "\r\n");
                return $retval;
            }
            case 'S': {
                // returns PDF as a string
                return $this->getBuffer();
            }
            default: {
                $this->Error('Incorrect output destination: '.$dest);
            }
        }
        return '';
    }


    protected function getBuffer() {
        return $this->buffer;
    }

    public static function fopenLocal($filename, $mode) {
        if (strpos($filename, '://') === false) {
            $filename = 'file://'.$filename;
        } elseif (stream_is_local($filename) !== true) {
            return false;
        }
        return fopen($filename, $mode);
    }

    protected function applyTSA($signature) {
        if (!$this->tsa_timestamp) {
            return $signature;
        }
        //@TODO: implement this feature
        return $signature;
    }

    public static function getObjFilename($type='tmp', $file_id='') {
        return tempnam(K_PATH_CACHE, '__tcpdf_'.$file_id.'_'.$type.'_'.md5(DomPdf::getRandomSeed()).'_');
    }

    public static function sendOutputData($data, $length) {
        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            // the content length may vary if the server is using compression
            header('Content-Length: '.$length);
        }
        echo $data;
    }

    public static function getRandomSeed($seed=''): string
    {
        $rnd = uniqid(rand().microtime(true), true);
        if (function_exists('posix_getpid')) {
            $rnd .= posix_getpid();
        }
        if (function_exists('openssl_random_pseudo_bytes') AND (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
            // this is not used on windows systems because it is very slow for a know bug
            $rnd .= openssl_random_pseudo_bytes(512);
        } else {
            for ($i = 0; $i < 23; ++$i) {
                $rnd .= uniqid('', true);
            }
        }
        return $rnd.$seed.__FILE__.serialize($_SERVER).microtime(true);
    }

    /**
     * @param $name
     * @return void
     */
    public function downloadPDFAsFile($name): void
    {
        if (ob_get_contents()) {
            $this->Error('Some data has already been output, can\'t send PDF file');
        }
        header('Content-Description: File Transfer');
        if (headers_sent()) {
            $this->Error('Some data has already been output to browser, can\'t send PDF file');
        }
        header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
        //header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        // force download dialog
        if (strpos(php_sapi_name(), 'cgi') === false) {
            header('Content-Type: application/force-download');
            header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
            header('Content-Type: application/pdf', false);
        } else {
            header('Content-Type: application/pdf');
        }
        // use the Content-Disposition header to supply a recommended filename
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
    }
}

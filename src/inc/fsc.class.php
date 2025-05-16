<?php

class Fsc
{
    const VERSION_NUMBER = '2.0.0';

    var $action = '';

    var $fscPath = '';

    var $showMenu = true;

    private $type = '';

    public function __construct($showMenu = true)
    {
        $this->fscPath = $_SERVER['DOCUMENT_ROOT'].'/var/cache/fsc/';

        $this->showMenu = $showMenu;

        if (!is_dir($this->fscPath)) {
            mkdir($this->fscPath);
        }
    }

    public function showMenu($msg='') {

        $url = $_SERVER['PHP_SELF'];

        $html = '
    <center><h2><a href="/cache.php" style="color:#33353f;">Full Page Cache</a></h2>
   <h3>'.$msg.'</h3>
    <form action="' . $url . '?action=use" id="use" method="post">
    <input type="submit" name="use" value="'.(($this->_checkFlag('use'))?'Dea':'A').'ctivate" style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'"/>
  </form><br />
  <form action="' . $url . '?action=show" id="show" method="post">
  	<input type="submit" name="show" value="Show Cached Pages" '.(($this->_checkFlag('show'))?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'" />
 </form><br>
  <hr>
  <form action="' . $url . '?action=activate" id="activate" method="post">
  	<input type="submit" name="activate" value="Cache One Page" '.(($this->_checkFlag())?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'" />
 </form><br />
  <form action="' . $url . '?action=permanent" id="permanent" method="post">
  	<input type="submit" name="permanent" value="Start Caching" '.(($this->_checkFlag('permanent'))?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'" />
 </form><br />
  <form action="' . $url . '?action=stop" id="stop" method="post">
  	<input type="submit" name="stop" value="Stop Caching" '.(($this->_checkFlag('stop'))?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'" />
 </form><br><hr><br>
  <form action="' . $url . '?action=reset" id="reset" method="post">
    <input type="submit" name="reset" value="Clear One Page" '.(($this->_checkFlag('reset'))?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'"/>
  </form><br>
  <form action="' . $url . '?action=resetall" id="reset" method="post">
    <input type="submit" name="resetall" value="Clear All" '.(($this->_checkFlag('resetall'))?' disabled="disabled"':'').' style="background-image: linear-gradient(to bottom, #FFF -33%, #c9c72e 33%); background-repeat:repeat-x; border: 1px solid #BFBC20; font-weight: normal; padding-top: 6px; padding-left: 12px; padding-right: 12px; padding-bottom: 6px; font-size: 12px; margin-left:8px; margin-top: 10px; cursor:pointer; font-family: Verdana,Helvetica,Geneva,Swiss,sans-serif;width:150px" onMouseOver="this.style.backgroundImage=\'\';this.style.backgroundColor=\'#c9c72e\'" onMouseOut="this.style.backgroundImage=\'linear-gradient(to bottom, #FFF -33%, #BFBC20 33%)\'"/>
  </form>

</center>';

        echo $html;

    }

    public function menuAction() {
        $this->showMenu();

        die();
    }

    public function resetallAction() {
        $files = array();
        $verzeichnis = $this->fscPath;

        $doNotDelete = array('.',
            '..',
            'active.flag.txt',
            'permanent.flag.txt',
            'use.flag.txt',
            '_files.txt');

        if ( is_dir ( $verzeichnis ))
        {
            // öffnen des Verzeichnisses
            if ( $handle = opendir($verzeichnis) )
            {
                // einlesen der Verzeichnisses
                while (($file = readdir($handle)) !== false)
                {
                    if (!in_array($file, $doNotDelete)) {
                        unlink($verzeichnis.$file);
                    }
                }
                closedir($handle);
            }
        }

        file_put_contents($this->fscPath.'_files.txt',serialize($files));
        if ($this->showMenu) $this->showMenu('All cache cleared.');

    }

    public function showAction() {
        $files = array();
        $verzeichnis =  $this->fscPath;

        $doNotDelete = array('.',
            '..',
            'active.flag.txt',
            'permanent.flag.txt',
            'use.flag.txt',
            '_files.txt');

        $files = unserialize(file_get_contents($this->fscPath.'_files.txt'));

        $paths = implode(', ', array_keys($files));

        if ($this->showMenu) $this->showMenu($paths);

    }

    public function useAction() {
        if (file_exists($this->fscPath.'use.flag.txt')) {
            unlink($this->fscPath . 'use.flag.txt');
        } else {
            file_put_contents($this->fscPath . 'use.flag.txt', $_SERVER['REMOTE_ADDR']);
        }

        if ($this->showMenu) $this->showMenu();
    }

    public function activateAction() {
        file_put_contents($this->fscPath.'active.flag.txt',$_SERVER['REMOTE_ADDR']);

        $this->type = 'active';

        if ($this->showMenu) $this->showMenu('One-time Caching activated.<br>Go to the page now that you want to cache.');

    }

    public function resetAction() {
        file_put_contents($this->fscPath.'reset.flag.txt',$_SERVER['REMOTE_ADDR']);

        if ($this->showMenu) $this->showMenu('One-time Clear Page Cache activated.<br>Go to the page now that you want to remove from the cache.');

    }

    public function permanentAction($ip = null) {
        $ip =$ip?$ip:$_SERVER['REMOTE_ADDR'];
        file_put_contents($this->fscPath.'permanent.flag.txt',$ip);

        if ($this->showMenu) $this->showMenu('Permanent Caching activated.');

    }

    public function stopAction() {
        if (file_exists($this->fscPath.'active.flag.txt')) {
            unlink($this->fscPath . 'active.flag.txt');
        }
        if (file_exists($this->fscPath.'permanent.flag.txt')) {
            unlink($this->fscPath . 'permanent.flag.txt');
        }

        if ($this->showMenu) $this->showMenu('All caching stopped.');
    }

    protected function _checkFlag($type='active') {
        if ($type == 'active') {
            return ((file_exists($this->fscPath.'active.flag.txt') AND $this->type != 'active') OR file_exists($this->fscPath.'permanent.flag.txt'));
        }

        if ($type == 'stop') {
            return (!file_exists($this->fscPath.'active.flag.txt') AND !file_exists($this->fscPath.'permanent.flag.txt'));
        }

        if ($type == 'reset') {
            return (file_exists($this->fscPath.'reset.flag.txt') OR (count(unserialize(file_get_contents($this->fscPath.'_files.txt'))) == 0));
        }

        if ($type == 'use') {
            return (file_exists($this->fscPath.'use.flag.txt'));
        }

        if ($type == 'resetall') {
            $files = unserialize(file_get_contents($this->fscPath.'_files.txt'));
            return (count($files) == 0);
        }
        return file_exists($this->fscPath.$type.'.flag.txt');
    }

    public function getWhitelistedCSRFActions()
    {
        return [
            'iframe',
            'menu',
            'show',
            'resetall',
            'use',
            'activate',
            'stop',
            'permanent',
            'reset'
        ];
    }


}
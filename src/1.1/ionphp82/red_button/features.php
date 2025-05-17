<?php
$keywords = 'Der Große Rote Button Quellcode Generator, The Big Red Button Source Code Generator';
$description = 'The Big Red Button Source Code Generator';
$title = 'The Big Red Button Source Code Generator Feature List';
require '../header.inc.php';
?>
<style type="text/css">
<!--
body {
	margin:20px;
}
dt {
  font-weight: bold;
  margin-top:10px
}
dl {
	margin-top:10px;
}

-->
</style>
</head>

<body>
<h1><img src="deutsche_flagge.gif" alt="deutsche Flagge"><a href="#english"><img border="0" src="englische_flagge.gif" alt="englische Flagge"></a></h1>
<h1><a href="index.php">Der Gro&szlig;e Rote Knopf Quellcode Generator</a></h1>
<p>Beim PHP-Programmieren ist es doch immer das Selbe. Man legt eine Datenbank-Tabelle an mit einigen Feldern (z.B. Vorname, Nachname, E-Mail) und tippt diese Feldnamen immer wieder in seiner Applikation. Einmal als schlichte Liste f&uuml;r die Feldnamen bei einem INSERT statement, dann in einfachen Hochkommas eingeschlossen, hier in doppelten. Ein anderes mal ist der Feldname schl&uuml;ssel in einem Array wie $_ POST oder $_SESSION. Obwohl  in der Tabellenstruktur in der Datenbank bereits der Typ genau festgelegt wurde, muss man ferner jedesmal  f&uuml;r die richtige Syntax &uuml;berlegen, ob es sich bei dem Feld um eine Zahl, einen Text oder eine Aufz&auml;hlungsliste  handelt und dies entsprechend ber&uuml;cksichtigen. Das Gleiche gilt beim Validieren und Escapen in einen anderen Kontext (SQL, HTML, PHP). Und wenn die Tabelle nicht nur Vor- und Nachname, sondern auch noch Datenfelder f&uuml;r Telefonnumer privat, Telefonnummer gesch&auml;ftlich, Fax, Mobil, Strasse, Hausnummer, Postleitzahl, Ort, Webseite, Notiz, undsoweiterundsofort umfasst, dann artet das regelm&auml;&szlig;ig in einer Fingerwundtipperei aus. Bein einer einfachen Anwendung, die nichts weiter macht als den Inhalt einer Datenbanktabelle anzuzeigen und es erlaubt, einzelne Eintr&auml;ge anzulegen und zu ver&auml;ndern, muss jeder Feldname ca. 25 Mal in verschiedenen Variablen-Formen geschrieben werden. Als PHP-Experte w&uuml;nscht man sich deshalb irgendwann einen Gro&szlig;en Roten Button, auf den man nur dr&uuml;cken muss, um diesen h&auml;ufig verwendeten Standard-Quellcode automatisch generieren zu lassen.<br>
  Nat&uuml;rlich steckt der Teufel im Detail und ohne Anpassung l&auml;sst sich mit dem Generator nur selten eine komplette Applikation erstellen, jedoch bietet sich stets mindestens einer der vom Gro&szlig;en Roten Button Quellcode Generator erstellten Bausteine  f&uuml;r  Copy &amp; Paste in die eigene Anwendung an.</p>
<h2>Feature Liste</h2>
<dl>
  <dd></dd>
  <dt>Open Source</dt>
  <dd>Der eigene <a href="source.php">Quellcode</a> des Gro&szlig;en Roten Button Quellcode Generators ist frei verf&uuml;gbar zum Erweitern und/oder Anpassen an die Ausgabe f&uuml;r ein eigenes Framework oder existierende (Zend Framework, jQuery, Dojo, iPhone iUI, ...).</dd>
  <dt>Eingabeformate</dt>
  <dd>In der gro&szlig;en Textbox ganz oben im Generator kann entweder eine kommaseparierte Liste von Variablennamen (z.B. &quot;vorname, nachname, strasse&quot;) eingetragen werden oder ein CREATE TABLE statement.</dd>
  <dt>Kommaseparierte Liste</dt>
  <dd>Den Variablen der kommagetrennten Liste kann man Buchstaben zur Kennzeichnung des Typs (&quot;i&quot; f&uuml;r Integer oder &quot;b&quot; f&uuml;r Bool) voranstellen. Das erste Feld ist hier immer der Prim&auml;rschl&uuml;ssel. Die Feldnamen k&ouml;nnen auch in Backticks eingeschlossen angegeben werden.<br>
  <u>Beispiel:</u><br>
  i geraet_id, name, beschreibung, b aktiv</dd>
  <dt> CREATE TABLE Statement</dt>
  <dd>Das  CREATE TABLE Statement wird zun&auml;chst intern in MySQL geparst. Es werden damit  im Vergleich zur kommagetrennten Liste weitere Datentypen erkannt: TEXT, ENUM, SET, TINYINT. Auch werden die Prim&auml;rschl&uuml;ssel, Default-Werte und, ob das Feld ein Pflichtfeld also nicht nullable ist, ausgelesen.</dd>
  <dt>Sektion<em> Target Variable</em>s</dt>
  <dd>Jedes einzelne Feld kann damit in verschiedenen Variablenformaten ausgegeben werden. Das Feld 'vorname' kann in vorname, Vorname, $vorname, ${vorname}  $_GET['vorname'],  $_POST['vorname'],  $_SESSION['vorname'],  $_REQUEST['vorname'] oder  $custom['vorname'] umgewandelt werden, wobei 'custom' wiederum f&uuml;r einen beliebigen Variablennamen steht. Diese Variablen werden vom Gro&szlig;en Roten Button Quellcode Generator standardm&auml;&szlig;ig als Liste ausgegeben. In der Sektion <em>Generated Code Type</em> kann man aber auch festlegen, dass diese Variablen in HTML Tags respektive Funktionsaufrufen eingeschlossen verwendet werden sollen oder damit SQL-Abfragen, Formulare und Listen erstellen.</dd>
  <dt>Sektion <em>Use function</em></dt>
  <dd>Um jede einzelne Variable in einen beliebigen Funktionsaufruf einzuschlie&szlig;en.</dd>
  <dt>Option <em>Multilingual</em></dt>
  <dd>Falls aktiv, wird die __() Methode zum &Uuml;bersetzen von Text eingef&uuml;gt</dd>
  <dt>Option <em>MySQL values</em></dt>
  <dd>Zur Erstellung einer SQL-Injection sicheren Datenbankabfrage unter Ber&uuml;cksichtigung der einzelnen Datentypen</dd>
  <dt>Option <em>Validation</em></dt>
  <dd>Hier erzeugt man mit dem Gro&szlig;en Roten Button Quellcode, welcher pr&uuml;ft, ob alle Pflichtfelder vorhanden sind und ob die Werte im G&uuml;ltigkeitsbereich des Datentyps f&uuml;r das jeweilige Feld liegen. Eine allgemeine oder f&uuml;r jedes Feld individuelle Fehlermeldungen k&ouml;nnen erstellt werden. Bei Bedarf erzeugt man hier auch Code, der die Variablen zum richtigen Typ konvertiert oder in die Session schreibt.</dd>
  <dt>Option <em>Form</em></dt>
  <dd>Integer oder bool'sche Felder werden zu Integer gecastet. Aus TINYINT(1) wird eine Checkbox erstellt, aus ENUM Feldern Radios, aus SET Feldern Auswahllisten und ansonsten Text-Felder. Zur  Cross-Site-Scripting sicheren Ausgabe in HTML wird die htmlspecialchars Funktion um die Variablen gelegt, welche je nach ausgew&auml;hltem Zeichensatz auch den  dritten Funktionsparameter setzt. Mit der Option <em>full</em> f&uuml;gt man hier auch gleich den Code f&uuml;r die oben erw&auml;hnte Datenvalidierung ein sowie die Logik zum Abspeichern eines neuen oder bestehenden Datensatzes.</dd>
  <dt>Option <em>List</em></dt>
  <dd>Der Quellcode f&uuml;r die Generierung einer Listenansicht der Tabelle wird hier ausgespuckt. Die Logik zum L&ouml;schen einer Zeile ist standardm&auml;&szlig;ig dabei, die Codebl&ouml;cke f&uuml;r Tabellensortierung, Suchfelder und Paginator sind zuschaltbar. F&uuml;r den Tabellenkopf kann man sich wahlweise den HTML-Code anzeigen lassen oder in PHP eine foreach-Schleife (Option <em>Dynmic Header</em>).</dd>
<dt>Option <em>HTML</em></dt>
  <dd>Zum Wrappen von HTML-Tags um Variablen. HTML-Attribute gehen dabei nicht verloren, unpaired HTML-Tags werden unterst&uuml;tzt. Zum Verschachteln von HTML Tags kann eine kommagetrennte Tag-Liste eingegeben werden.</dd>
<dt>Mehrere Prim&auml;rschl&uuml;ssel</dt>
  <dd>Mehrere Prim&auml;rschl&uuml;ssel werden nur bei Eingabe eines CREATE TABLE statements unterst&uuml;tzt</dd>
<dt>&nbsp;</dt>
  <dt>Zeichensatz</dt>
  <dd>Unterst&uuml;tzt werden ISO-8859-1 und UTF-8. Relevant ist das beim Quellcode f&uuml;r die Validierung, f&uuml;r das Formular und die Liste.</dd>
</dl>
<dl>
  <h1><a name="english"><img src="englische_flagge.gif" alt="englische Flagge"></h1>
  <h1><a href="index.php">The Big Red Button Source Code Generator</a></h1>
  <p>As you may know, building a simple web application that displays the content of a database table and a form to edit it, requires typing the field names over and over again. Once you write them as a simple comma-separated list, then they come via HTTP POST method packed in $_POST['foobar']. For context change you have to write code to escape and protect against SQL-Injection or Cross Site Scripting attacks. Most of the time you must consider the type of each field even though you defined it already in the database. After all you must type every field name  about 25 times in different formats for a basic application. The Big Red Button Source Code Generator, being developed by Christian Fraunholz, is designed to help you with this annoying task.</p>
  <h2>Feature List</h2>
  <dl>
    <dd></dd>
    <dt>Open Source</dt>
    <dd>The Source Code of The Big Red Button Source Code Generator is available <a href="source.php">here</a>. Feel free to extend or customize it for your own application design or an existing one (e.g. Zend Framework, jQuery, Dojo, iPhone iUI, ...).</dd>
    <dt>Input Formats</dt>
    <dd>You can enter either a comma-separated list of variable names (e.g. &quot;firstname, lastname, street&quot;) or a CREATE TABLE statement.</dd>
    <dt>Comma-separated list</dt>
    <dd>An optional &quot;i&quot; before the variable name indicates the type integer, a leading &quot;b&quot; a bool field. The first fieldname becomes the primary key. Enclosing the names in backticks is allowed.<u><br>
      Example:</u><br>
i item_id, name, description, b active</dd>
    <dt> CREATE TABLE Statement</dt>
    <dd>MySQL parses the CREATE TABLE statement and does recognize additional types TEXT, ENUM, SET, TINYINT, all primary keys, the mandatory (not nullable) fields  and  default values.</dd>
    <dt>Section <em>Target Variables</em></dt>
    <dd>The Big Red Button Source Code Generator outputs the field names in various formats.  You can convert firstname to Firstname, $firstname, ${firstname}  $_GET['firstname'],  $_POST['firstname'],  $_SESSION['firstname'],  $_REQUEST['firstname'] oder  $custom['firstname'], where 'custom' is a variable name of your choise. The Generator creates a simple list by default, but in the section <em>Generated Code Type</em> this setting is also being used for the variable syntax in HTML tags, function calls, SQL statements, forms and lists.</dd>
    <dt>Section <em>Use function</em></dt>
    <dd>To use a function for each variable.</dd>
    <dt>Option <em>Multilingual</em></dt>
    <dd>Activate this option for multilingual applications and add the __() method for string translation.</dd>
    <dt>Option <em>MySQL values</em></dt>
    <dd>Generates a INSERT statement with the variables protected againt SQL-Injection depending on its type.</dd>
    <dt>Option <em>Validation</em></dt>
    <dd>Creates source code that checks for the existence of mandatory fields and for the right type (INTEGER, BOOL, SET, ENUM). Generates error messages for each field or a general one. You can choose to add the type convertion logic or to write the variables into the  session.</dd>
    <dt>Option <em>Form</em></dt>
    <dd>Creates code to cast integer or bool values to integer. Creates a checkbox for TINYINT(1) fields, radio buttons for ENUM fields, a drop-down box for SET fields and a text field otherwise. Uses the htmlspecialchars function with the third parameter when building UTF-8 code. With the  <em>full</em> option activated, pressing the Big Red Button will also generate the code for the data validation, a SQL SELECT statement and logic to save a new or existing entry into the database for you.</dd>
    <dt>Option <em>List</em></dt>
    <dd>To retrieve the source code to generate a list view out of the table, to delete one row and - if you want - logic to sort the table when clicking on a table header, search fields and a pagination. For the header  you can choose  HTML-code or a PHP foreach loop.</dd>
    <dt>Option<em> HTML</em></dt>
    <dd>Wraps one or multiple (comma-separated) HTML tags around your variables. Preserves HTML attributes.</dd>
    <dt>Multiple primary keys</dt>
    <dd>Multiple primary keys are supported only for CREATE TABLE statements</dd>
    <dt>Charset, </dt>
    <dd>Supports ISO-8859-1 and UTF-8. Used in validation, form and list code.</dd>
  </dl>
</dl>
</body>
</html>


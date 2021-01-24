<html>
 <head>
  <title>MSSE692 Test</title>
 </head>
 <body>
 <?php
    echo '<p>Hello MSSE692</p>';
   
    $myPDO = new PDO('mysql:host=db;dbname=msse692',
	    /* username = */ 'msse692',
	    /* password = */ 'P@55w0rd');

    echo 'MySQL Version: ' . $myPDO->query('select version()')->fetchColumn();

    echo phpinfo();

    ?>
 </body>
</html>

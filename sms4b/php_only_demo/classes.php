<?
require_once "options.php";
require_once "CSms4bBase.php";

error_reporting(E_ERROR);



if ($LOGIN == '' || $PASSWORD ==''):?>
	<h1 style="color:red">�� ������� ����� � ������ ��� �������� SMS. ������� � ���� options.php � ������� ����� � ������.</h1>
	<p><b>��������</b></p>
	<div style="padding-left:15px">
	<p><b>�������������</b></p>
	<pre>
		#��� ����� ��������� ��� ��������� ��� ������� � �������� SMS
		$LOGIN = '';	// ��� ����� ��� �������� SMS �� ������� ��������
		$PASSWORD = ''; // ��� ������ ��� ��������
	</pre>
	
	<p><b>������ ����</b></p>
	<pre>
		#��� ����� ��������� ��� ��������� ��� ������� � �������� SMS
		$LOGIN = 'SMS_TEST';	// ��� ����� ��� �������� SMS �� ������� ��������
		$PASSWORD = '1b013def'; // ��� ������ ��� ��������
	</pre>
	</div>
	<?exit();?>
<?endif;?>

<?
if (!is_object($SMS4B))
	$SMS4B = new Csms4bBase($LOGIN,$PASSWORD);
?>
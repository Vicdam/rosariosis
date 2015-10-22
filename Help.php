<?php

require_once 'Warehouse.php';

$help_translated = 'Help_' . mb_substr( $locale, 0, 2 ) . '.php';
$help_english = 'Help_en.php';

if ( file_exists( $help_translated ) ) //FJ translated help
	require_once $help_translated;

else
	require_once $help_english;

//FJ add help for non-core modules
$not_core_modules = array_diff( array_keys( $RosarioModules ), $RosarioCoreModules );

foreach ( (array)$not_core_modules as $not_core_module )
{
	$not_core_dir = 'modules/' . $not_core_module . '/';

	if ( file_exists( $not_core_dir . $help_translated ) ) //FJ translated help
		require_once $not_core_dir . $help_translated;

	elseif ( file_exists( $not_core_dir . $help_english ) )
		require_once $not_core_dir . $help_english;
}

switch( User( 'PROFILE' ) )
{
	case 'admin':
		$title = _( 'Administrator' );
	break;

	case 'teacher':
		$title = _( 'Teacher' );
	break;

	case 'parent':
		$title = _( 'Parent' );
	break;
	
	case 'student':
		$title = _( 'Student' );
	break;
}

$handle = PDFStart();
?>

<TABLE>
	<TR>
		<TD>
			<IMG SRC="assets/themes/<?php echo Preferences( 'THEME' ); ?>/logo.png" />
		</TD>
		<TD>
			<h1>&nbsp;<?php echo sprintf( _( '%s Handbook' ), $title ); ?></h1>
		</TD>
	</TR>
</TABLE>
<HR />

<?php
foreach ( (array)$help as $program => $value )
{
	$_REQUEST['modname'] = $program;

	if ( mb_strpos( $program, '/' ) )
	{
		$modcat = mb_substr( $program, 0, mb_strpos( $program, '/' ) );

		if ( !$RosarioModules[$modcat] ) //module not activated
			break;
	
		if ( $modcat != $old_modcat
			&& $modcat != 'Custom' ) : ?>

			<div style="page-break-after: always;"></div>

<?php
			unset( $_ROSARIO['DrawHeader'] );

			$_ROSARIO['HeaderIcon'] = 'modules/' . $modcat . '/icon.png';

			$modcat_echo = str_replace( '_', ' ',  $modcat );

			echo DrawHeader( _( $modcat_echo ) );
?>
			<HR />

		<?php
		endif;

		if ( $modcat != 'Custom' )
			$old_modcat = $modcat;
	}
?>

<div style="page-break-inside: avoid;">
	<h3>

<?php
	if ( $program == 'default' )
		echo ParseMLField( Config( 'TITLE' ) )
			. ' - ' . sprintf( _( '%s Handbook' ), $title ) . '<BR />'
			. sprintf( _( 'version %s' ), '1.1' );

	else
		echo ( ProgramTitle() == 'RosarioSIS' ? $program : ProgramTitle() );
?>

	</h3>
	<TABLE class="width-100p">
		<TR>
			<TD class="header2">

<?php
	if ( User( 'PROFILE' ) == 'student' )
		$value = str_replace(
			'your child',
			'yourself',
			str_replace( 'your child\'s', 'your', $value ) );

	$value = str_replace( 'RosarioSIS', Config( 'NAME' ), $value );

	echo $value;
?>

			</TD>
		</TR>
	</TABLE>
</div>
<BR />

<?php
} //end foreach
?>

<div style="text-align: center; font-weight: bold;">
	<a href="http://www.rosariosis.org/">http://www.rosariosis.org/</a>
</div>

<?php

$_REQUEST['modname'] = '';

PDFStop( $handle );

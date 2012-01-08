<?php
/*
 * Copyright (c) 2011 Andrew E. Bruno <aeb@qnot.org> 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('needleman-wunsch-class.php');

$match = isset($_GET['match']) ? $_GET['match'] : 1;
$mismatch = isset($_GET['mis']) ? $_GET['mis'] : 0;
$gap = isset($_GET['gap']) ? $_GET['gap'] : -1;
$seq1 = isset($_GET['seq1']) ? $_GET['seq1'] : 'ACAGTCGAACG';
$seq2 = isset($_GET['seq2']) ? $_GET['seq2'] : 'ACCGTCCG';

if(!is_numeric($match)) $match = 1;
if(!is_numeric($mismatch)) $mismatch = 0;
if(!is_numeric($gap)) $gap = -1;

if(empty($seq1)) $seq1 = 'ACAGTCGAACG';
if(empty($seq2)) $seq2 = 'ACCGTCCG';

if(strlen($seq1) > 15) $seq1 = substr($seq1, 0, 25);
if(strlen($seq2) > 15) $seq2 = substr($seq2, 0, 25);

$nw = new NeedlemanWunsch($match, $mismatch, $gap);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <title>Needleman-Wunsch Alignment Score Table</title>
    <style type="text/css">
    .trace { background-color: #c99;font-weight: bold }
    .seq { background-color: #ccc;}
    .data { border-collapse: collapse }
    .data td { border: 1px solid #666; text-align: center; }
    .align td { text-align: center; }
    .config { border-collapse: collapse }
    .config td { font-size:small;border: 1px solid #ccc; text-align: left; padding: 5px;}
    </style>
</head>
<body>
<h4>Needleman-Wunsch Alignment</h4>
<form method="get">
<table class="config">
<tr>
    <td>Match Score: </td><td><input type="text" size="2" name="match" value="<?php echo htmlentities($match);?>" /></td>
    <td>Mis-match Score: </td><td><input type="text" size="2" name="mis" value="<?php echo htmlentities($mismatch);?>" /></td>
    <td>Gap Penalty: </td><td><input type="text" size="2" name="gap" value="<?php echo htmlentities($gap);?>"/></td>
</tr>
<tr>
    <td>Sequence 1: </td><td colspan="6"><input type="text" name="seq1" size="15" value="<?php echo htmlentities($seq1);?>"/></td>
</tr>
<tr>
    <td>Sequence 2: </td><td colspan="6"><input type="text" name="seq2" size="15" value="<?php echo htmlentities($seq2);?>" /></td>
</tr>
<tr>
    <td colspan="6"><input type="submit" value="Compute"/></td>
</tr>
</table>
</form>
<?php $nw->renderAsHTML($seq1, $seq2, false); ?>
</body>
</html>

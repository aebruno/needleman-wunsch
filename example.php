<?php
require_once('needleman-wunsch-class.php');

$nw = new NeedlemanWunsch(1, 0, -1);
$seq1 = 'ACAGTCGAACG';
$seq2 = 'ACCGTCCG';

// Print to screen
$nw->renderAsASCII($seq1, $seq2);

// Full Page HTML
//$nw->renderAsHTML($seq1, $seq2, true);
?>

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

/**
 * This class implements the Needleman-Wunsch global alignment algorithm and
 * was created for educational purposes to demonstrate how the algorithm works.
 * It is not intended for use in real sequence alignment or use with very large
 * sequences. It computes the alignment scoring table and provides methods to
 * display the alignment table and optimal global alignment in both HTML
 * and ASCII format. 
 */
class NeedlemanWunsch {
    private static $arrow_up = '&#8593;';
    private static $arrow_left = '&#8592;';
    private static $arrow_nw = '&#8598;';

    private $match_score = 1;
    private $mismatch_score = 0;
    private $gap_penalty = -1;

    private $matrix = array();
    private $optimal_alignment = array();

    /**
     * Constructor
     */
    public function __construct($match_score, $mismatch_score, $gap_penalty) {
        $this->match_score = $match_score;
        $this->mismatch_score = $mismatch_score;
        $this->gap_penalty = $gap_penalty;
    }

    /**
     * Computes the Needleman-Wunsch global alignment and returns a data structure
     * representing the alignment table.
     */
    public function compute($seq1, $seq2) {
        $this->init($seq1, $seq2);

        for($i = 1; $i < count($this->matrix); $i++) {
            for($j = 1; $j < count($this->matrix[$i]); $j++) {
                $match_mismatch = ($seq1[$i-1] === $seq2[$j-1]) ? $this->match_score : $this->mismatch_score;
                $match = $this->matrix[$i-1][$j-1]['val'] + $match_mismatch;
                $hgap = $this->matrix[$i-1][$j]['val'] + $this->gap_penalty;
                $vgap = $this->matrix[$i][$j-1]['val'] + $this->gap_penalty;
                $max = max($match, $hgap, $vgap);
                $pointer = self::$arrow_nw;
                if($max === $hgap) {
                    $pointer = self::$arrow_up;
                } else if($max === $vgap) {
                    $pointer = self::$arrow_left;
                }

                $this->matrix[$i][$j]['pointer'] = $pointer;
                $this->matrix[$i][$j]['val'] = $max;
            }
        }


        $i = count($this->matrix)-1;
        $j = count($this->matrix[0])-1;

        $this->optimal_alignment['seq1'] = array();
        $this->optimal_alignment['seq2'] = array();
        $this->optimal_alignment['aln'] = array();
        $this->optimal_alignment['score'] = $this->matrix[$i][$j]['val'];

        while($i !== 0 and $j !== 0) {
            $base1 = $seq1[$i-1];
            $base2 = $seq2[$j-1];
            $this->matrix[$i][$j]['trace'] = true;
            $pointer = $this->matrix[$i][$j]['pointer'];


            if($pointer === self::$arrow_nw) {
                $i--;
                $j--;
                $this->optimal_alignment['seq1'][] = $base1;
                $this->optimal_alignment['seq2'][] = $base2;
                $this->optimal_alignment['aln'][] = ($base1 === $base2) ? '|' : ' ';
            } else if($pointer === self::$arrow_up) {
                $i--;
                $this->optimal_alignment['seq1'][] = $base1;
                $this->optimal_alignment['seq2'][] = '-';
                $this->optimal_alignment['aln'][] = ' ';
            } else if($pointer === self::$arrow_left) {
                $j--;
                $this->optimal_alignment['seq1'][] = '-';
                $this->optimal_alignment['seq2'][] = $base2;
                $this->optimal_alignment['aln'][] = ' ';
            } else {
                die("Invalid pointer: $i,$j");
            }
        }

        foreach(array('seq1', 'seq2', 'aln') as $k) {
            $this->optimal_alignment[$k] = array_reverse($this->optimal_alignment[$k]);
        }

        return $this->matrix;
    }

    /**
     * Returns the optimal alignment data structure
     */
    public function getOptimalGlobalAlignment() {
        return $this->optimal_alignment;
    }

    /**
     * Computes the Needleman-Wunsch global alignment and displays the results in HTML.
     */
    public function renderAsHTML($seq1, $seq2, $full_page=true) {
        $this->compute($seq1, $seq2);

        if($full_page) { 
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>';
            echo '<meta http-equiv="content-type" content="text/html;charset=utf-8" />';
            echo '<meta name="description" content="" />';
            echo '<meta name="keywords" content="" />';
            echo '<title>Needleman-Wunsch Alignment Score Table</title>';
            echo '<style type="text/css">';
            echo '.trace { background-color: #c99;font-weight: bold }';
            echo '.seq { background-color: #ccc;}';
            echo '.data { border-collapse: collapse }';
            echo '.data td { border: 1px solid #666; text-align: center; }';
            echo '.align td { text-align: center; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
        }
        echo '<h3>Alignment Score Table</h3>';
        echo '<table class="data">';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td>';
        for($i = 0; $i < strlen($seq2); $i++) {
            echo '<td class="seq">'.$seq2[$i].'</td>';
        }
        echo '</tr>';
        for($i = 0; $i < count($this->matrix); $i++) {
            echo "<tr>\n";
            if($i > 0) {
                echo '<td class="seq">'.$seq1[$i-1].'</td>'; 
            } else  {
                echo '<td>&nbsp;</td>';
            }

            foreach($this->matrix[$i] as $r) {
                $str = '<td';
                $str .= $r['trace'] ? ' class="trace">' : '>';
                $str .= ($r['pointer'] !== null) ? $r['pointer'] : '&nbsp;';
                $str .= '&nbsp;';
                $str .= $r['val'] < 0 ? $r['val'] : '&nbsp;'.$r['val'];
                $str .= '</td>';
                echo $str;
            } 
            
            echo "</tr>\n"; 
        }
        echo "\n</table>";
        echo '<h3>Optimal Global Alignment (score = '.$this->optimal_alignment['score'].')</h3>';
        echo '<table class="align">';
        foreach(array('seq2', 'aln', 'seq1') as $k) {
            echo '<tr>';
            foreach($this->optimal_alignment[$k] as $v) {
                echo "<td>$v</td>";
            }
            echo '</tr>';
        }
        echo "\n</table>";

        if($full_page) echo '</body></html>';
    }

    /**
     * Computes the Needleman-Wunsch global alignment and displays the results in ASCII.
     */
    public function renderAsASCII($seq1, $seq2) {
        $this->compute($seq1, $seq2);

        echo "Alignment Score Table\n\n";

        $char_array = array();
        for($i = 0; $i < strlen($seq2); $i++) {
            $char_array[] = '   '.$seq2[$i];
        }
        echo "\t\t".implode("\t", $char_array)."\n";
        for($i = 0; $i < count($this->matrix); $i++) {
            if($i > 0) {
                echo $seq1[$i-1]; 
            } else  {
                echo ' ';
            }
            echo "\t";

            $char_array = array();
            foreach($this->matrix[$i] as $r) {
                $str = ($r['pointer'] !== null) ? html_entity_decode($r['pointer'], ENT_QUOTES, 'UTF-8') : ' ';
                $str .= ' ';
                $str .= $r['val'] < 0 ? $r['val'] : ' '.$r['val'];
                $str .= $r['trace'] ? '*' : ' ';
                $char_array[] = $str;
            } 
            echo implode("\t", $char_array);
            echo "\n";
        }

        echo "\nOptimal Global Alignment (score = ".$this->optimal_alignment['score'].")\n";
        foreach(array('seq2', 'aln', 'seq1') as $k) {
            echo implode(' ', $this->optimal_alignment[$k])."\n";
        }
    }

    /**
     * Initialization
     */
    private function init($seq1, $seq2) {
        $this->matrix = array();
        $this->optimal_alignment = array();
        for($i = 0; $i < strlen($seq1)+1; $i++) {
            for($j = 0; $j < strlen($seq2)+1; $j++) {
                $this->matrix[$i][$j] = array(
                    'pointer' => null, 
                    'trace' => null, 
                    'val' => 0
                );
            }
        }

        for($i = 0; $i < strlen($seq1); $i++) {
            $this->matrix[$i+1][0]['val'] = ($i+1) * $this->gap_penalty;
        }

        for($j = 0; $j < strlen($seq2); $j++) {
            $this->matrix[0][$j+1]['val'] = ($j+1) * $this->gap_penalty;
        }
    }
}

?>

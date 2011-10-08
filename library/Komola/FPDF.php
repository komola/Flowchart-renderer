<?php

require_once dirname(__FILE__) ."/../FPDF/fpdf.php";

class Komola_FPDF extends FPDF
{
	function Header()
	{
		// Select Arial bold 15
		$this->SetFont('Arial','B',40);
		// Move to the right
		// Framed title
		$this->Cell($this->CurPageSize[1],30,'Wireless Wolfsburg - Authenticate User',0,0,'C');
	}

	function MultiCell($w, $h, $txt, $border=0, $align="J", $fill = false)
	{
		parent::MultiCell($w, $h, utf8_decode($txt), $border, $align, $fill);
	}

	function MultiCellHeight($w, $h, $txt, $border=0, $align='J', $fill=false)
	{
		$beginningY = $this->getY();
		// Output text with automatic or explicit line breaks
		$cw = &$this->CurrentFont['cw'];
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
		$s = str_replace("\r",'',$txt);
		$nb = strlen($s);
		if($nb>0 && $s[$nb-1]=="\n")
			$nb--;
		$b = 0;
		if($border)
		{
			if($border==1)
			{
				$border = 'LTRB';
				$b = 'LRT';
				$b2 = 'LR';
			}
			else
			{
				$b2 = '';
				if(strpos($border,'L')!==false)
					$b2 .= 'L';
				if(strpos($border,'R')!==false)
					$b2 .= 'R';
				$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
			}
		}
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$ns = 0;
		$nl = 1;
		while($i<$nb)
		{
			// Get next character
			$c = $s[$i];
			if($c=="\n")
			{
				// Explicit line break
				if($this->ws>0)
				{
					$this->ws = 0;
					$this->_out('0 Tw');
				}
				//$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
				$this->setX($w);
				$this->setY($this->getY() + $h);
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if($border && $nl==2)
					$b = $b2;
				continue;
			}
			if($c==' ')
			{
				$sep = $i;
				$ls = $l;
				$ns++;
			}
			$l += $cw[$c];
			if($l>$wmax)
			{
				// Automatic line break
				if($sep==-1)
				{
					if($i==$j)
						$i++;
					if($this->ws>0)
					{
						$this->ws = 0;
						$this->_out('0 Tw');
					}
					//$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
					$this->setX($w);
					$this->setY($this->getY() + $h);
				}
				else
				{
					if($align=='J')
					{
						$this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
					}
					//$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
					$this->setX($w);
					$this->setY($this->getY() + $h);
					$i = $sep+1;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if($border && $nl==2)
					$b = $b2;
			}
			else
				$i++;
		}
		// Last chunk
		if($this->ws>0)
		{
			$this->ws = 0;
			$this->_out('0 Tw');
		}
		if($border && strpos($border,'B')!==false)
			$b .= 'B';
		//$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
		$this->setX($w);
		$this->setY($this->getY() + $h);
		$this->x = $this->lMargin;

		$height = $this->getY() - $beginningY;
		$this->setY($beginningY);


		return $height;
	}

}

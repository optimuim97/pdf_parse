<?php

namespace App\Controller;

use App\Entity\Transaction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Config;
use Symfony\Component\HttpFoundation\Request;

class PdfController extends AbstractController
{
    public $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    #[Route('/parse_pdf_solution_one', name: 'app_pdf_one', methods:['POST'])]
    public function one(Request $request)
    {
        $config = new \Smalot\PdfParser\Config();
        $config->setHorizontalOffset('');
        // $file = $request->files->get('pdf');

        $parser = new Parser([],$config);

        $pdf = $parser->parseFile('documents/TRANSACTIONS-TPE-02-12-2022-APAYM-TPE-MID-2020000330.pdf');

        $text = $pdf->getText();
        var_dump($text);
        die();
        
        return  $this->json($text);
    }


    #[Route('/parse_pdf_solution_two', name: 'app_pdf', methods:['POST'])]
    public function two(Request $request)
    {
        $config = new \Smalot\PdfParser\Config();
        $config->setHorizontalOffset('t');

        $parser = new Parser([],$config);
        
        $pdf = $parser->parseFile('documents/TRANSACTIONS-TPE-02-12-2022-APAYM-TPE-MID-2020000330.pdf');
        $text = $pdf->getText();

        $x = str_replace("\nTransaction\n", "[TRANSACTION]", $text);
        $separed_part = explode("[TRANSACTION]", $x); 

        $part1 = $separed_part[1];
        $separed_part1 = str_replace("\nOrabank","[ORABANK]",$part1);
        $part1_final = explode("[ORABANK]",$separed_part1);
        $part1_final = $part1_final[0];

        $part2 = $separed_part[2];
        $separed_part2 = str_replace("\nOrabank","[ORABANK]",$part2);
        $part2_prefinal = explode("[ORABANK]",$separed_part2);


        $part2_prefinal = str_replace("ID. TPEtNombre", "[OK]",$part2_prefinal[0]);
        $part2_prefinal = explode("[OK]",$part2_prefinal);
        $part2_final = $part2_prefinal[0];

        $trans1=(explode("\n",$part1_final));
        $trans2=(explode("\n",$part2_final));

        $list_transactions_array = array_merge($trans1, $trans2);

        // dd($list_transactions_array);
        $list = new ArrayCollection();

        foreach($list_transactions_array as $item){
            
            if($item != ""){
                $row =  explode("t",$item);
    
                $transaction = new Transaction();
                $transaction->setDate($row[0]);
                $transaction->setHeure($row[1]);
                $transaction->setNumeroCarte($row[2]);
                $transaction->setTypeCarte($row[3]);
                $transaction->setCodeAuth($row[4]);
                $transaction->setMontantTransaction($row[5]);
                $transaction->setTypeTransaction($row[5]);
    
                $this->em->persist($transaction);
                $this->em->flush();
    
                $list->add($transaction);
            }
        
        }   

        return  $this->json(
            $list
        );
    }

}

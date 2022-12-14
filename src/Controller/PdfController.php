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

    #[Route('/parse_pdf', name: 'app_pdf', methods:['POST'])]
    public function two(Request $request)
    {
        $config = new \Smalot\PdfParser\Config();
        $config->setHorizontalOffset('\t');
        $information_array = null;

        $untreated_rows = [];
        $list_transaction = new ArrayCollection();

        $parser = new Parser([],$config);

        $file = $request->files->get('pdf');
        $pdf = $parser->parseFile($file);

        $pdfDetails = $pdf->getDetails();
        $number_page =intval($pdfDetails["Pages"]);

        for ($i=0; $i < $number_page; $i++) { 
            $text = $pdf->getPages()[$i]->getText();

            if($i==0){
                $chunck =explode("Transaction\n", $text);
                $information_array = explode("\n",$chunck[0]);

                $group_translist = explode("Orabank,", $chunck[1])[0];

                $group_translist =explode("\n",$group_translist);

                foreach ($group_translist as $key => $value) {
                    if($value == ""){
                        unset($group_translist[$key]);
                    }                    
                } 

                array_push($untreated_rows,$group_translist);

            }else{

                if( $i == ($number_page - 1)){

                    $chunck =explode("Transaction\n",$text);
                    $group_translist= explode("ID. TPE",$chunck[1])[0];

                    // $group_translist = str_replace('\\',"",$group_translist);
                    
                   array_push($untreated_rows,$group_translist);

                }else{
                    $chunck =explode("Transaction\t\n",$text);
                    $group_translist= explode("Orabank,",$chunck[2])[0];
                    
                    $group_translist =explode("\n",$group_translist);
    
                    foreach ($group_translist as $key => $value) {
                        if($value == ""){
                            unset($group_translist[$key]);
                        }                    
                    } 
                    array_push($untreated_rows,$group_translist);

                    
                }

            }
            
        }
        
        return $this->json($untreated_rows);

        $text = $pdf->getText();

        // return $this->json($pdf );
        // $pdf = $parser->parseFile('documents/TRANSACTIONS-TPE-02-12-2022-APAYM-TPE-MID-2020000330.pdf');
        $text = $this->json([
            $pdfDetails,
            $text
        ]);

        return $this->json($text);

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
                $transaction->setTypeTransaction($row[6]);
    
                // $this->em->persist($transaction);
                // $this->em->flush();
    
                $list->add($transaction);
            }
        
        }   

        return  $this->json(
            $list
        );
    }

}

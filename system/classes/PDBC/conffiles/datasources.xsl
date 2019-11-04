<xsl:stylesheet version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output method="xml" indent="yes"
        doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
        doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>

    <!--XHTML document outline-->
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>DataSources-XML</title>
                <style type="text/css">
                    td, th      { width: 40%; border: 1px solid silver; padding: 2px; }
                    td:first-child, th:first-child  { width: 20%; }
                    table       { width: 650px; border-collapse:collapse; }
                </style>
            </head>
            <body>
                <xsl:apply-templates/>
            </body>
        </html>
    </xsl:template>

    <!--Table headers and outline-->
    <xsl:template match="datasources/*">
        <img src="stock_data-sources-hand-32.png"/><br/><xsl:value-of select="@name"/> [
          <strong><xsl:apply-templates select="@type"/></strong>]
        <table>
          <tr>
            <td>Host:Port</td>
            <td><xsl:apply-templates select="host"/>:<xsl:apply-templates select="port"/></td>
          </tr>
          <tr>
            <td>SID(Database)</td>
            <td><xsl:apply-templates select="sid"/></td>
          </tr>
          <tr>
            <td>User/Password</td>
            <td><xsl:apply-templates select="username"/>/<xsl:apply-templates select="userpassword"/></td>
          </tr>
        </table>
        <p/>
    </xsl:template>


</xsl:stylesheet>
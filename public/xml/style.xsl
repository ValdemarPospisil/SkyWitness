<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
  <xsl:output method="html" indent="yes"/>

  <xsl:template match="/ufo-sightings">
    <html>
      <head>
        <title>UFO Sightings</title>
        <style>
          body { font-family: sans-serif; background: #111; color: #eee; padding: 2em; }
          .sighting { margin-bottom: 2em; border-bottom: 1px solid #444; padding-bottom: 1em; }
          h2 { color: #0f0; }
        </style>
      </head>
      <body>
        <h1>UFO Sightings</h1>
        <xsl:for-each select="sighting">
          <div class="sighting">
            <h2><xsl:value-of select="date-time"/></h2>
            <p><strong>Location:</strong> <xsl:value-of select="locale"/>, <xsl:value-of select="region"/> (<xsl:value-of select="country"/>)</p>
            <p><strong>Shape:</strong> <xsl:value-of select="ufo-shape"/></p>
            <p><strong>Duration:</strong> <xsl:value-of select="encounter-duration"/></p>
            <p><strong>Description:</strong> <xsl:value-of select="description"/></p>
          </div>
        </xsl:for-each>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>

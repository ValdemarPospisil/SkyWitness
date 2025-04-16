<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="UTF-8" indent="yes" doctype-public="-//W3C//DTD HTML 4.01//EN" doctype-system="http://www.w3.org/TR/html4/strict.dtd"/>

<xsl:template match="/">
  <html>
  <head>
    <title>SkyWitness UFO Sightings</title>
    <style>
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f5f5f5;
      }
      header {
        background: linear-gradient(to right, #1a237e, #311b92);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }
      h1 {
        margin: 0;
        font-size: 2.5rem;
      }
      .metadata {
        background-color: #e8eaf6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 5px solid #3f51b5;
      }
      .sighting {
        background-color: white;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-top: 3px solid #3f51b5;
      }
      .sighting:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        transform: translateY(-2px);
        transition: all 0.3s ease;
      }
      .sighting-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
      }
      .sighting-id {
        background-color: #3f51b5;
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
      }
      .location {
        background-color: #e8f5e9;
        padding: 10px;
        border-radius: 6px;
        margin: 10px 0;
      }
      .shape-badge {
        background-color: #673ab7;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
        text-transform: uppercase;
        font-size: 0.8rem;
        font-weight: bold;
        letter-spacing: 1px;
      }
      .date-time {
        font-weight: bold;
        color: #3f51b5;
      }
      .witness-report {
        background-color: #fff9c4;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        border-left: 3px solid #ffc107;
        font-style: italic;
      }
      .report-heading {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
        color: #5d4037;
      }
      .footer {
        text-align: center;
        margin-top: 30px;
        color: #666;
        font-size: 0.9rem;
      }
    </style>
  </head>
  <body>
    <header>
      <h1>SkyWitness UFO Sightings</h1>
      <p>Exported data from SkyWitness Database</p>
      <p>Total sightings: <xsl:value-of select="count(//sighting)"/> | Exported on: <xsl:value-of select="/ufo_sightings/@exported_at"/></p>
    </header>
    
    <div class="metadata">
      <h2><xsl:value-of select="//metadata/title"/></h2>
      <p><xsl:value-of select="//metadata/description"/></p>
    </div>
    
    <xsl:for-each select="//sighting">
      <div class="sighting">
        <div class="sighting-header">
          <span class="date-time"><xsl:value-of select="date_time"/></span>
          <span class="sighting-id">ID: <xsl:value-of select="@id"/></span>
        </div>
        
        <div>
          <div><strong>Year:</strong> <xsl:value-of select="year"/></div>
          <div><strong>Season:</strong> <xsl:value-of select="season"/></div>
          
          <div class="location">
            <strong>Location:</strong>
            <div>
              <xsl:value-of select="location/country"/>
              <xsl:if test="location/region != ''">
                , <xsl:value-of select="location/region"/>
              </xsl:if>
            </div>
            
            <xsl:if test="location/geo_coordinates">
              <div>
                <strong>Coordinates:</strong> 
                Lat: <xsl:value-of select="location/geo_coordinates/latitude"/>, 
                Long: <xsl:value-of select="location/geo_coordinates/longitude"/>
              </div>
            </xsl:if>
          </div>
          
          <xsl:if test="shape">
            <div><strong>UFO Shape:</strong> <span class="shape-badge"><xsl:value-of select="shape"/></span></div>
          </xsl:if>
          
          <xsl:if test="duration">
            <div><strong>Duration:</strong> <xsl:value-of select="duration"/></div>
          </xsl:if>
          
          <xsl:if test="witness_report">
            <div class="witness-report">
              <span class="report-heading">Witness Report:</span>
              <xsl:value-of select="witness_report"/>
            </div>
          </xsl:if>
        </div>
      </div>
    </xsl:for-each>
    
    <div class="footer">
      <p>SkyWitness - UFO Sightings Database</p>
      <p>Generated with XSL transformation</p>
    </div>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>
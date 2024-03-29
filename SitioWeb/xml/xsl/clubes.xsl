<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <table border="1" class="clubestabla">
            <tbody>
                <tr>
                    <th class="color" colspan="4">Clubes Temporada <xsl:value-of select="clubes/tempActual" />
                    </th>
                </tr>
                <tr>
                    <th>Escudo</th>
                    <th>Nombre</th>
                    <th>Entrenador</th>
                </tr>
                <xsl:variable name="maxCount" select="9" />
                <xsl:variable name="rowCount" select="count(clubes/equipo[nombre != 'Equipo para Descansar'])" />
                <xsl:for-each select="clubes/equipo[nombre != 'Equipo para Descansar']">
                    <tr>
                        <td>
                            <a href="#" id="{nombre}" class="enlaces">
                                <img class="escu">
                                    <xsl:attribute name="src">
                                        <xsl:choose>
                                            <xsl:when test="string-length(escudo/imagen) &gt; 0">
                                                <xsl:value-of select="escudo/imagen"/>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:text>../imagenes/escudos/escudo.png
                                                </xsl:text>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:attribute>
                                    <xsl:attribute name="alt">Escudo</xsl:attribute>
                                </img>
                            </a>
                        </td>
                        <td>
                            <a href="#" class="enlaces" id="{nombre}">
                                <xsl:value-of select="nombre"/>
                            </a>
                        </td>
                        <td>
                            <xsl:value-of select="entrenador"/>
                        </td>
                        <td>
                            <xsl:value-of select="telefono"/>
                        </td>
                    </tr>
                    <xsl:if test="position() &lt; $rowCount or position() &lt; $maxCount">
                        <tr>
                            <td colspan="4">
                                <hr/>
                            </td>
                        </tr>
                    </xsl:if>
                </xsl:for-each>
            </tbody>
        </table>
    </xsl:template>
</xsl:stylesheet>

<?php
/**
 * mincals.inc.php
 * Fonctions permettant d'afficher le mini calendrier
 * Ce script fait partie de l'application GRR
 * Dernière modification : $Date: 2010-01-06 10:21:20 $
 * @author    Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @copyright Copyright 2003-2008 Laurent Delineau
 * @link      http://www.gnu.org/licenses/licenses.html
 * @package   root
 * @version   $Id: mincals.inc.php,v 1.7 2010-01-06 10:21:20 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * $Log: mincals.inc.php,v $
 * Revision 1.7  2010-01-06 10:21:20  grr
 * *** empty log message ***
 *
 * Revision 1.6  2008-11-16 22:00:59  grr
 * *** empty log message ***
 *
 *
 */
function minicals($year, $month, $day, $area, $room, $dmy)
{


global $display_day, $vocab;

// Récupération des données concernant l'affichage du planning du domaine
get_planning_area_values($area);

// PHP Calendar Class
// Copyright David Wilkinson 2000. All Rights reserved.
// This software may be used, modified and distributed freely
// providing this copyright notice remains intact at the head
// of the file.
// This software is freeware. The author accepts no liability for
// any loss or damages whatsoever incurred directly or indirectly
// from the use of this script.
// URL:   http://www.cascade.org.uk/software/php/calendar/
// Email: davidw@cascade.org.uk

    #constructeur de la classe calendar
    class Calendar
    {
    var $month;
    var $year;
    var $day;
    var $h;
    var $area;
    var $room;
    var $dmy;
    var $week;
    var $mois_precedent;
    var $mois_suivant;
    function Calendar($day, $month, $year, $h, $area, $room, $dmy, $mois_precedent, $mois_suivant)
    {
        $this->day   = $day;
        $this->month = $month;
        $this->year  = $year;
        $this->h     = $h;
        $this->area  = $area;
        $this->room  = $room;
        $this->dmy   = $dmy;
        $this->mois_precedent = $mois_precedent;
        $this->mois_suivant = $mois_suivant;
    }
    function getCalendarLink($month, $year)
    {
        return "";
    }
    #Liens vers une une date donnée.
    function getDateLink($day, $month, $year)
        {
       global $vocab;
      if ($this->dmy=='day') return "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_day"))."\" href=\"".$this->dmy.".php?year=$year&amp;month=$month&amp;day=$day&amp;area=".$this->area."\"";
      if ($this->dmy!='day') return "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_day"))."\" href=\"day.php?year=$year&amp;month=$month&amp;day=$day&amp;area=".$this->area."\"";

    }

    function getHTML()
    {
        global $weekstarts, $vocab, $type_month_all, $display_day, $nb_display_day;
        // Calcul de la date courante
        $date_today = mktime(12, 0, 0, $this->month, $this->day, $this->year);
        // Calcul du numéro de semaine courante
        $week_today = numero_semaine($date_today);
        if (!isset($weekstarts)) $weekstarts = 0;
        $s = "";
        $daysInMonth = getDaysInMonth($this->month, $this->year);
        // Calcul de la date au 1er du mois de la date courante
        $date = mktime(12, 0, 0, $this->month, 1, $this->year);
        $first = (strftime("%w",$date) + 7 - $weekstarts) % 7;
        $monthName = utf8_strftime("%B",$date);
        $prevMonth = $this->getCalendarLink($this->month - 1 >   0 ? $this->month - 1 : 12, $this->month - 1 >   0 ? $this->year : $this->year - 1);
        $nextMonth = $this->getCalendarLink($this->month + 1 <= 12 ? $this->month + 1 :  1, $this->month + 1 <= 12 ? $this->year : $this->year + 1);
        $s .= "<table border = \"0\" class=\"calendar\">\n";
        $s .= "<tr><td></td>\n";
        if (($this->h) and (($this->dmy=='month') or ($this->dmy=='month_all') or ($this->dmy=='month_all2') )) $bg_lign = "week"; else $bg_lign = 'calendarHeader';
        $s .= "<td align=\"center\" valign=\"top\" class=\"$bg_lign\" colspan=\"".$nb_display_day."\">";
            #Permet de récupérer le numéro de la 1ere semaine affichée par le mini calendrier.
//            $week = number_format(strftime("%W",$date),0);
            $week = numero_semaine($date);
   			$weekd = $week;
        // on ajoute un lien vers le mois précédent
        if ($this->mois_precedent == 1) {
          $tmp = mktime(0, 0, 0, ($this->month)-1, 1, $this->year);
          $lastmonth = date("m",$tmp);
          $lastyear= date("Y",$tmp);
          if (($this->dmy!='day') and ($this->dmy!='week_all') and ($this->dmy!='month_all') and ($this->dmy!='month_all2'))
           $s .= "<a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$lastyear&amp;month=$lastmonth&amp;day=1&amp;area=$this->area&amp;room=$this->room\">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;";
          else
           $s .= "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_month"))."\" href=\"".$type_month_all.".php?year=$lastyear&amp;month=$lastmonth&amp;day=1&amp;area=$this->area\">&lt;&lt;</a>&nbsp;&nbsp;&nbsp;";
        }

            if (($this->dmy!='day') and ($this->dmy!='week_all') and ($this->dmy!='month_all') and ($this->dmy!='month_all2'))
         $s .= "<a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$this->year&amp;month=$this->month&amp;day=1&amp;area=$this->area&amp;room=$this->room\">$monthName&nbsp;$this->year</a>";
            else
         $s .= "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_month"))."\" href=\"".$type_month_all.".php?year=$this->year&amp;month=$this->month&amp;day=1&amp;area=$this->area\">$monthName&nbsp;$this->year</a>";
        // on ajoute un lien vers le mois suivant
        if ($this->mois_suivant == 1) {
          $tmp = mktime(0, 0, 0, ($this->month)+1, 1, $this->year);
          $nextmonth = date("m",$tmp);
          $nextyear= date("Y",$tmp);
          if (($this->dmy!='day') and ($this->dmy!='week_all') and ($this->dmy!='month_all') and ($this->dmy!='month_all2'))
           $s .= "&nbsp;&nbsp;&nbsp;<a title=\"".htmlspecialchars(get_vocab("see_month_for_this_room"))."\" href=\"month.php?year=$nextyear&amp;month=$nextmonth&amp;day=1&amp;area=$this->area&amp;room=$this->room\">&gt;&gt;</a>";
          else
           $s .= "&nbsp;&nbsp;&nbsp;<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_the_month"))."\" href=\"".$type_month_all.".php?year=$nextyear&amp;month=$nextmonth&amp;day=1&amp;area=$this->area\">&gt;&gt;</a>";
        }
        $s .= "</td>\n";
        $s .= "</tr>\n";
        $s .= "<tr><td></td>\n";
        $s .= getFirstDays();
        $s .= "</tr>\n";
        $d = 1 - $first;
        $temp = 1;
        while ($d <= $daysInMonth)
        {
//            if (($date_today <= $date) and ($this->h) and (($this->dmy=='week_all') or ($this->dmy=='week') )) $bg_lign = " class=\"week\""; else $bg_lign = '';
            if (($week_today == $week) and ($this->h) and (($this->dmy=='week_all') or ($this->dmy=='week') )) $bg_lign = " class=\"week\""; else $bg_lign = '';
            $s .= "<tr ".$bg_lign."><td class=\"calendarcol1\" align=\"right\" valign=\"top\">";
            #Affichage du numéro de la semaine en cours à droite du calendrier et génère un lien sur la semaine voulue.
            if (($this->dmy!='day') and ($this->dmy!='week_all') and ($this->dmy!='month_all') and ($this->dmy!='month_all2'))
               $s .="<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_room"))."\" href=\"week.php?year=$this->year&amp;month=$this->month&amp;day=$temp&amp;area=$this->area&amp;room=$this->room\">s".sprintf("%02d",$week)."</a>";
            else
                $s .="<a title=\"".htmlspecialchars(get_vocab("see_week_for_this_area"))."\" href=\"week_all.php?year=$this->year&amp;month=$this->month&amp;day=$temp&amp;area=$this->area\">s".sprintf("%02d",$week)."</a>";
            $temp=$temp+7;
            while ((!checkdate($this->month, $temp, $this->year)) and ($temp > 0))  $temp--;


            #Nouveau affichage, affiche le numéro de la semaine dans l'année.Incrémentation de ce numéro à chaque nouvelle semaine.
            $date = mktime(12, 0, 0, $this->month, $temp, $this->year);
            $week = numero_semaine($date);

            $s .= "</td>\n";
            for ($i = 0; $i < 7; $i++)
            {
                $j = ($i + 7 + $weekstarts) % 7;
                if ($display_day[$j] == "1") {// début condition "on n'affiche pas tous les jours de la semaine"
				if (($this->dmy == 'day') and ($d == $this->day) and ($this->h))
					$s .= "<td class=\"week\" align=\"right\" valign=\"top\">";
				else
	                $s .= "<td class=\"calendar\" align=\"right\" valign=\"top\">";
                if ($d > 0 && $d <= $daysInMonth)
                {
                    $link = $this->getDateLink($d, $this->month, $this->year);
                    if ($link == "")
                        $s .= $d;
                        #Permet de colorer la date affichée sur la page
                    elseif (($d == $this->day) and ($this->h))
                        $s .= $link."><span class=\"cal_current_day\">$d</span></a>";
                    else
                        $s .= $link.">$d</a>";
                }
                else
                    $s .= "&nbsp;";
                  $s .= "</td>\n";
                }// fin condition "on n'affiche pas tous les jours de la semaine"
                $d++;
            }
            $s .= "</tr>\n";
        }
        if ($week-$weekd<6) $s .= "<tr><td>&nbsp;</td></tr>";
        $s .= "</table>\n";
        return $s;
    }
    }
    $nb_calendar = getSettingValue("nb_calendar");
    if ($nb_calendar >= 1) {
     if ($nb_calendar % 2 == 1)
       $milieu = ($nb_calendar+1)/2;
     else
       $milieu = $nb_calendar/2;
     // Les mois avant le mois courant
     for ($k=1;$k<$milieu;$k++) {
        $month_[]=mktime(0, 0, 0, $month+$k-$milieu, 1, $year);
     }
     // Le mois courant
     $month_[] = mktime(0, 0, 0, $month, $day, $year);
     // Les mois après le mois courant
     for ($k=$milieu;$k<$nb_calendar;$k++) {
        $month_[]=mktime(0, 0, 0, $month+$k-$milieu+1, 1, $year);
     }

     $ind=1;
     foreach ($month_ as $key) {
      if ($ind == 1)
        $mois_precedent=1;
      else
        $mois_precedent=0;
      if ($ind == $nb_calendar)
        $mois_suivant=1;
      else
        $mois_suivant=0;

      if ($ind == $milieu)
        $flag_surlignage=1;
      else
        $flag_surlignage=0;
      echo "<td>";$cal = new Calendar(date("d",$key), date("m",$key), date("Y",$key), $flag_surlignage, $area, $room, $dmy, $mois_precedent, $mois_suivant);
      echo $cal->getHTML();
//      if ($ind == $milieu) echo "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_several_months"))."\" href=\"year.php?area=$area\">".$vocab["viewyear"]."</a>";
      echo "</td>";
      $ind++;
     }
    }
    // Affichage du lien "plusieurs mois"
    echo "<td>";
    echo "<a title=\"".htmlspecialchars(get_vocab("see_all_the_rooms_for_several_months"))."\" href=\"year.php?area=$area\">".$vocab["viewyear"]."</a>";
    echo "</td>";

}?>
/**
 * Created by JetBrains PhpStorm.
 * User: fanny
 * Date: 28/04/11
 * Time: 11:53
 * To change this template use File | Settings | File Templates.
 */

  /*
    Append a text box if a person has too many jokers.
  */
  function refreshJ(url)
  {
    $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {},
           success: function(data)
           {
                if(!data.error)
                {
                    if(!data.nobody)
                    {
                         $("#maxJokers").show();
                         for(var i=0;i<data.name.length;i++)
                         {
                            $("#maxJokers").append("<p> <b>Alerte : </b> "+data.name[i]+" ("+data.uid[i]+") a "+data.nbjocker[i]+" jokers !</p>");
                         }
                     }
                }
           }
    })
  }

  /*
    Append a textbox if a category has anymore products available
  */
  function refreshP(url)
  {
    $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {},
           success: function(data)
           {
               if(!data.error && data.name != null)
                {
                     $("#maxProduct").show();
                     for(var i=0;i<data.name.length;i++)
                     {
                        $("#maxProduct").append("<p><i>Alerte</i> : Attention ! Il ne reste plus qu'<b>1 "+data.name[i]+"</b> en stock !</p>");
                     }
                }
           }
    })
  }

  /*
    Get number of lends, number of products and number of products lended thanks pies chart and a line chart.
  */
  function ajaxDate(date,url)
  {
    $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {date: date},
           success: function(data)
           {
                var titre = $("#date").val();
                xticks = [[0, 'lund'], [1, 'mard'], [2, 'merc'], [3, 'jeud'], [4, 'vend']];
                yticks = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
                line1 = [[0,data[0]], [1, data[1]], [2,data[2]], [3,data[3]], [4,data[4]]];
                if(data.pie)
                    pieVal = eval(data.pie);
                else
                    pieVal = [[]];
                if(data.pie2)
                    pieVal2 = eval(data.pie2);
                else
                    pieVal2 = [[]];

                pie = $.jqplot('pieChart', [pieVal], {
                    title: 'Produits empruntés',
                    seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},
                    legend:{show:true}
                });

                pie2 = $.jqplot('pieChart2', [pieVal2], {
                    title: 'Produits pas encore rendus',
                    seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},
                    legend:{show:true},
                    axesDefaults:
                    {
                        tickOptions: {

                        }
                    }
                });

                plot4 = $.jqplot('conteneur', [line1],
                {
                    highlighter: {
                        tooltipAxes: 'y'},
                    legend : { show : true},
                    title :  "Nombre d'emprunts",
                    grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                    series:
                    [
                        { label:'nombre d\'emprunts' }
                    ],
                    axes:
                    {
                         xaxis:{ticks:xticks},
                         yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                    }
                });
             }
    });
  }

  /*
    Return a line chart of lends for a week for one product
  */
  function ajaxDateForOneProduct(date,id,url)
  {
    $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {date: date, id: id},
           success: function(data)
           {
                xticks = [[0, 'lund'], [1, 'mard'], [2, 'merc'], [3, 'jeud'], [4, 'vend']];
                yticks = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
                line1= [[0,data[0]], [1, data[1]], [2,data[2]], [3,data[3]], [4,data[4]]];

                plot4 = $.jqplot('conteneur', [line1],
                {
                    highlighter: {
                        tooltipAxes: 'y'},
                    legend : { show : true},
                    title :  "Nb emprunts de "+data.name,
                    grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                    series:
                    [
                        { label:'nombre d\'emprunts' }
                    ],
                    axes:
                    {
                         xaxis:{ticks:xticks,max:31,min:0,tickInterval:1},
                         yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                    }
                });
             }
    });
    }

    /*
    Return a line chart of lends for a month for one product
    */
    function ajaxMois(mois,id,url)
    {
        $.ajax({
               type: 'POST',
               url: url,
               dataType: 'json',
               data: {mois: mois, id: id},
               success: function(data)
               {
                    xticks = [[0, '1'], [1, '3'], [2, '5'], [3, '7'], [4, '9'], [5, '11'], [6, '13'], [7, '15'], [8, '17'], [9, '19'], [10, '21'], [11, '23'], [12, '25'], [13, '27'], [14, '29'], [15, '31']];
                    yticks = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
                    line1 = [[0,data[0]], [0.5, data[1]], [1,data[2]], [1.5,data[3]], [2,data[4]], [2.5,data[5]], [3, data[6]], [3.5,data[7]], [4,data[8]], [4.5,data[9]], [5,data[10]], [5.5, data[11]], [6,data[12]], [6.5,data[13]], [7, data[14]], [7.5,data[15]], [8,data[16]], [8.5, data[17]], [9,data[18]], [9.5,data[19]], [10, data[20]], [10.5,data[21]], [11,data[22]], [11.5, data[23]], [12,data[24]], [12.5,data[25]], [13, data[26]], [13.5,data[27]], [14,data[28]], [14.5, data[29]], [15.5,data[30]], [16, data[31]]];

                    plot4 = $.jqplot('conteneur', [line1],
                    {
                        highlighter: {
                            tooltipAxes: 'y'},
                        legend : { show : true},
                        title :  "Nb emprunts de "+data.name,
                        grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                        series:
                        [
                            { label:'nombre d\'emprunts' }
                        ],
                        axes:
                        {
                             xaxis:{
                                rendererOptions:{tickRenderer:$.jqplot.CanvasAxisTickRenderer},
                                ticks:xticks,
                                tickOptions:{angle:-10}
                             },
                             yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                        },
                        cursor: {show: false}
                    });
                 }
        });
    }

    /*
    Return a line chart of lends for a year for one product
    */
   function ajaxAnnee(annee,id,url)
   {
        $.ajax({
               type: 'POST',
               url: url,
               dataType: 'json',
               data: {annee: annee, id: id},
               success: function(data)
               {
                    xticks = [[0, 'janv'], [1, 'fevr'], [2, 'mars'], [3, 'avr'], [4, 'mai'], [5, 'juin'], [6, 'juil'], [7, 'aou'], [8, 'sept'], [9, 'oct'], [10, 'nov'], [11, 'dec']];
                    yticks = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120];
                    line1 = [[0,data[0]], [1, data[1]], [2,data[2]], [3,data[3]], [4,data[4]], [5,data[5]], [6, data[6]], [7,data[7]], [8,data[8]], [9,data[9]], [10,data[10]], [11, data[11]]];

                    plot4 = $.jqplot('conteneur', [line1],
                    {
                        highlighter: {
                            tooltipAxes: 'y'},
                        legend : { show : true},
                        title :  "Nb emprunts de "+data.name,
                        grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                        series:
                        [
                            { label:'nombre d\'emprunts' }
                        ],
                        axes:
                        {
                             xaxis:{ticks:xticks},
                             yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                        }
                    });
                 }
         });
   }

   /*
    Return a line chart of lends for a year for one category
   */
   function ajaxDateOneCateg(date,id,url)
   {
        $.ajax({
               type: 'POST',
               url: url,
               dataType: 'json',
               data: {date: date, id: id},
               success: function(data)
               {
                    xticks = [[0, 'lund'], [1, 'mard'], [2, 'merc'], [3, 'jeud'], [4, 'vend']];
                    yticks = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
                    line1=[[0,data[0]], [1, data[1]], [2,data[2]], [3,data[3]], [4,data[4]]];
                    line2=[[0,data.total[0]], [1, data.total[1]], [2,data.total[2]], [3,data.total[3]], [4,data.total[4]]];

                    plot4 = $.jqplot('conteneur', [line1, line2],
                    {
                        highlighter: {
                            tooltipAxes: 'y'},
                        legend : { show : true},
                        title :  "Nb emprunts de "+data.name,
                        seriesDefaults: {showMarker:false},
                        grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                        series:
                        [
                            { label:'nombre d\'emprunts' },
                            {label:'nombre de produits dans cette catégorie'}
                        ],
                        axes:
                        {
                             xaxis:{ticks:xticks},
                             yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                        }
                    });
                 }
        });
    }

    /*
    Return a line chart of lends for a month for one category
   */
    function ajaxMoisOneCateg(mois,id,url)
    {
    $.ajax({
           type: 'POST',
           url: url,
           dataType: 'json',
           data: {mois: mois, id: id},
           success: function(data)
           {
                xticks = [[0, '1'], [1, '3'], [2, '5'], [3, '7'], [4, '9'], [5, '11'], [6, '13'], [7, '15'], [8, '17'], [9, '19'], [10, '21'], [11, '23'], [12, '25'], [13, '27'], [14, '29'], [15, '31']];
                yticks = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
                line1 = [[0,data[0]], [0.5, data[1]], [1,data[2]], [1.5,data[3]], [2,data[4]], [2.5,data[5]], [3, data[6]], [3.5,data[7]], [4,data[8]], [4.5,data[9]], [5,data[10]], [5.5, data[11]], [6,data[12]], [6.5,data[13]], [7, data[14]], [7.5,data[15]], [8,data[16]], [8.5, data[17]], [9,data[18]], [9.5,data[19]], [10, data[20]], [10.5,data[21]], [11,data[22]], [11.5, data[23]], [12,data[24]], [12.5,data[25]], [13, data[26]], [13.5,data[27]], [14,data[28]], [14.5, data[29]], [15.5,data[30]], [16, data[31]]];
                line2 = [[0,data.total[0]], [0.5, data.total[1]], [1,data.total[2]], [1.5,data.total[3]], [2,data.total[4]], [2.5,data.total[5]], [3, data.total[6]], [3.5,data.total[7]], [4,data.total[8]], [4.5,data.total[9]], [5,data.total[10]], [5.5, data.total[11]], [6,data.total[12]], [6.5,data.total[13]], [7, data.total[14]], [7.5,data.total[15]], [8,data.total[16]], [8.5, data.total[17]], [9,data.total[18]], [9.5,data.total[19]], [10, data.total[20]], [10.5,data.total[21]], [11,data.total[22]], [11.5, data.total[23]], [12,data.total[24]], [12.5,data.total[25]], [13, data.total[26]], [13.5,data.total[27]], [14,data.total[28]], [14.5, data.total[29]], [15.5,data.total[30]], [16, data.total[31]]];

                plot4 = $.jqplot('conteneur', [line1,line2],
                {
                    highlighter: {
                        tooltipAxes: 'y'},
                    legend : { show : true},
                    title :  "Nb emprunts de "+data.name,
                    seriesDefaults: {showMarker:false},
                    grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                    series:
                    [
                        { label:'nombre d\'emprunts' },
                        {label:'nombre de produits dans cette catégorie'}
                    ],
                    axes:
                    {
                         xaxis:{ticks:xticks},
                         yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                    }
                });
             }
    });
    }

    /*
    Return a line chart of lends for a year for one category
    */
    function ajaxAnneeOneCateg(annee,id,url)
    {
        $.ajax({
               type: 'POST',
               url: url,
               dataType: 'json',
               data: {annee: annee, id: id},
               success: function(data)
               {
                    xticks = [[0, 'janv'], [1, 'fevr'], [2, 'mars'], [3, 'avr'], [4, 'mai'], [5, 'juin'], [6, 'juil'], [7, 'aou'], [8, 'sept'], [9, 'oct'], [10, 'nov'], [11, 'dec']];
                    yticks = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120];
                    line1 = [[0,data[0]], [1, data[1]], [2,data[2]], [3,data[3]], [4,data[4]], [5,data[5]], [6, data[6]], [7,data[7]], [8,data[8]], [9,data[9]], [10,data[10]], [11, data[11]]];
                    line2 = [[0,data.total[0]], [1, data.total[1]], [2,data.total[2]], [3,data.total[3]], [4,data.total[4]], [5,data.total[5]], [6, data.total[6]], [7,data.total[7]], [8,data.total[8]], [9,data.total[9]], [10,data.total[10]], [11, data.total[11]]];

                    plot4 = $.jqplot('conteneur', [line1,line2],
                    {
                        highlighter: {
                            tooltipAxes: 'y'},
                        legend : { show : true},
                        title :  "Nb emprunts de "+data.name,
                        seriesDefaults: {showMarker:false},
                        grid: {background:'#f3f3f3', gridLineColor:'#accf9b'},
                        series:
                        [
                            { label:'nombre d\'emprunts' },
                            {label:'nombre de produits dans cette catégorie'}
                        ],
                        axes:
                        {
                             xaxis:{ticks:xticks},
                             yaxis:{ticks:yticks, tickOptions:{formatString:'%d'}}
                        }
                    });
                 }
        });
    }
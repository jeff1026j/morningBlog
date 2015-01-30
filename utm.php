<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
        <head>
    <meta charset="UTF-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">

    <link href="css/custom.css" rel="stylesheet">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>

    <style type="text/css">
        .col-centered{
            float: none;
            margin: 0 auto;
        }
        .navbar-default {
            background-color: #a0ce4e !important;
        }
        .navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:hover, .navbar-default .navbar-nav>.open>a:focus {
            background-color: #E9F8DC !important;
            }
        /*#wrapper{
            margin-top: 60px;
        }
        #container{
            margin-top: 100px;
        }*/
        .formcolumn{
            padding: 30px 40px 30px 40px;
            background-color: white;
            border: solid 1px;
            border-color: #BEFFBB;
            max-width: 500px;
        }
    </style>
    </head>
        <body class="index">
                <script type="text/javascript">

                var medium_init = { '首頁Banner':'HomeBanner','文上banner' : 'A1_Banner', '文下banner' : 'A2_Banner', '文下alert' : 'inAlert','sidebar特別活動banner' : 'S1_Banner', 'sidebar下banner':'S2_Banner','文章內容關鍵字':'inArticle'};
                var source = {'官方部落格' : 'official-blog','廣告' : 'FB1' , '粉絲頁' : 'FBPage', '電子報' : 'NewsLetter','新聞' : 'iHealthNews', 'GigaCircle' : 'GigaCircle',
			      'Instagram'  : 'instagram' , 'PTT' : 'PTT'
		};
                var medium = { 'official-blog' : medium_init,
                               'FBPage' : { '早餐吃麥片':'FanPage', '其他免費': 'others'},
			       'FB1' : { '廣告' : 'FB', '部落客':'blgr'},
                               'NewsLetter' : {'email':'email'},
                               'iHealthNews' : medium_init,
			       'instagram' : {'官方':'official', '其他':'others'},
			       'PTT' : {'PTT':'PTT'}
								
                                        };
                var campaignName = {'產品頁面':'', '類別頁':'Cat', '標籤' : 'Tab', '首頁' : 'Shop'};

                </script>

                <div class="container">

                  <div id="wrapper">
                    <div id="container" class="container">
                      <div class="main">
                        <div class="row">
                          <div class="formcolumn col-xs-6 col-centered">
					<h2>輸入連結：</h2>

                                        <div id='UrlInput'>
					<input type="text" id="urlinput"  class="form-control" placeholder="url">
                                        </div>

                                        <h2>連結要放在哪：</h2>

                                        <div id='SourceSelect'>

                                        </div>
					
					
					<h2>詳細位置：</h2>
                                        <div id='MediumSelect'>

                                        </div>

					<h2>要推薦什麼？</h2>
					<input type="text" id="nameinput"  class="form-control" placeholder="產品title、類別名、標籤名">
                                        <div id='NameSelect'>

                                        </div>

					<h2>廣告內容(好判別、另外加上自己名字方便 Track)：</h2>
                                        <div id='ContentSelect'>
						<input type="text" id="contentinput"  class="form-control" placeholder="ex: BenefitOfRedBean、或文章標題">
                                        </div>
					
					<br/><br/>
					<button id="genButton" type="button" class="btn btn-success btn-lg">產生</button>
					
					<h2>生成網址:</h2>
					<textarea id="resultText"  class="form-control" rows="4"></textarea>

					
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>



                <script type="text/javascript">
		//get date time
		var today=new Date();
		var month=new Array();
		month[0]="Jan";
		month[1]="Feb";
		month[2]="Mar";
		month[3]="Apr";
		month[4]="May";
		month[5]="June";
		month[6]="July";
		month[7]="Aug";
		month[8]="Sep";
		month[9]="Oct";
		month[10]="Nov";
		month[11]="Dec";
		//var n = month[d.getMonth()];


                var sourcehtml = '';
                var i = 0;
                for (var key in source) {

                   sourcehtml = sourcehtml+'<div class="radio"><label><input type="radio" name="optionsRadios" id="optionsRadios'+i+'" value="'+source[key]+'">'+key+'</label></div>';
                   i=i+1;
                }

                $("#SourceSelect").html(sourcehtml);
                //select first button first~
//                $("#optionsRadios0").prop('checked',true); $("#optionsRadios0").click();

		$('#SourceSelect :radio').on('click',function(){
//                    alert('xxx'+$(this).val());
                    var mediumhtml = '';
                    var k = 0;
                    var medium_source = medium[$(this).val()];
                    for (var key in medium_source) {

                       mediumhtml = mediumhtml+'<div class="radio"><label><input type="radio" name="mediumRadios" id="mediumRadios'+k+'" value="'+medium_source[key]+'">'+key+'</label></div>';
                       k=k+1;
                    }

//                    console.log('xxxx: '+mediumhtml);
                    $("#MediumSelect").html(mediumhtml);
                    $("#mediumRadios0").prop('checked',true); $("#mediumRadios0").click();

                });
		
		//generate result
		$('#genButton').on('click',function(){

			//parse url
			var resultURL = $('#urlinput').val();
			var searchIndex = resultURL.search("\\?");
//			console.log('xx: '+searchIndex);
			if(searchIndex > -1){
				resultURL = resultURL.substring(0,searchIndex);
			}
			
			//get utmsource
			var utmsource = $('input[name=optionsRadios]:checked').val();
			var utmmedium = $('input[name=mediumRadios]:checked').val();
			var utmName = $('input[name=nameRadios]:checked').val()+'-'+$('#nameinput').val();
			var utmContent = today.getFullYear().toString()+(today.getMonth()+1).toString()+today.getDate().toString()+'-'+$('#contentinput').val();

			var resultUrl = resultURL+'?utm_source=' + utmsource + '&utm_medium='+utmmedium+'&utm_content='+utmContent+'&utm_campaign='+utmName;

			$('#resultText').text(resultUrl);

		});

		var namehtml = '';
                var j = 0;
                for (var key in campaignName) {

                   namehtml = namehtml+'<div class="radio"><label><input type="radio" name="nameRadios" id="nameRadios'+j+'" value="'+today.getFullYear()+'-'+month[today.getMonth()]+'-'+campaignName[key]+'">'+key+'</label></div>';
                   j=j+1;
                }
		$("#NameSelect").html(namehtml);
		$("#optionsRadios0").prop('checked',true); $("#optionsRadios0").click();
                </script>
        </body>
</html>


<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Surat Pengantar - {{config('configs.app_name')}}</title>

        <!-- Report Office2013 style -->
        <link href="{{asset('adminlte/component/stimulsoft/css/stimulsoft.viewer.office2013.whiteblue.css')}}" rel="stylesheet">
        
        <!-- Stimusloft Reports.JS -->
        <script src="{{asset('adminlte/component/stimulsoft/js/stimulsoft.reports.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminlte/component/stimulsoft/js/stimulsoft.viewer.js')}}" type="text/javascript"></script>
        
        <script type="text/javascript">
            StiHelper.prototype.process = function (args, callback) {
                if (args) {
                    if (args.event == "BeginProcessData") {
                        args.preventDefault = true;
                        if (args.database == "XML" || args.database == "JSON") return callback(null);
                    }
                    var command = {};
                    for (var p in args) {
                        if (p == "report" && args.report != null) command.report = JSON.parse(args.report.saveToJsonString());
                        else if (p == "settings" && args.settings != null) command.settings = args.settings;
                        else if (p == "data") command.data = Stimulsoft.System.Convert.toBase64String(args.data);
                        else command[p] = args[p];
                    }
                    
                    var json = JSON.stringify(command);
                    if (!callback) callback = function (message) {
                        if (Stimulsoft.System.StiError.errorMessageForm && !String.isNullOrEmpty(message)) {
                            var obj = JSON.parse(message);
                            if (!obj.success || !String.isNullOrEmpty(obj.notice)) {
                                var message = String.isNullOrEmpty(obj.notice) ? "There was some error" : obj.notice;
                                Stimulsoft.System.StiError.errorMessageForm.show(message, obj.success);
                            }
                        }
                    }
                    jsHelper.send(json, callback);
                }
            }
            
            StiHelper.prototype.send = function (json, callback) {
                try {
                    var request = new XMLHttpRequest();
                    request.open("post", this.url, true);
                    request.timeout = this.timeout * 1000;
                    request.onload = function () {
                        if (request.status == 200) {
                            var responseText = request.responseText;
                            request.abort();
                            callback(responseText);
                        }
                        else {
                            Stimulsoft.System.StiError.showError("[" + request.status + "] " + request.statusText, false);
                        }
                    };
                    request.onerror = function (e) {
                        var errorMessage = "Connect to remote error: [" + request.status + "] " + request.statusText;
                        Stimulsoft.System.StiError.showError(errorMessage, false);
                    };
                    request.send(json);
                }
                catch (e) {
                    var errorMessage = "Connect to remote error: " + e.message;
                    Stimulsoft.System.StiError.showError(errorMessage, false);
                    request.abort();
                }
            };
            
            StiHelper.prototype.getUrlVars = function (json, callback) {
                var vars = {};
                var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
                    function (m, key, value) {
                        vars[key] = value;
                });
                return vars;
            }
            
            function StiHelper(url, timeout) {
                this.url = url;
                this.timeout = timeout;
            }
            
            jsHelper = new StiHelper("{{url('handler.php')}}", 30);
        </script>
        <script type="text/javascript">
            Stimulsoft.Base.StiLicense.key = "6vJhGtLLLz2GNviWmUTrhSqnOItdDwjBylQzQcAOiHn0s4gy0Fr5YoUZ9V00Y0igCSFQzwEqYBh/N77k4f0fWXTHW5rqeBNLkaurJDenJ9o97TyqHs9HfvINK18Uwzsc/bG01Rq+x3H3Rf+g7AY92gvWmp7VA2Uxa30Q97f61siWz2dE5kdBVcCnSFzC6awE74JzDcJMj8OuxplqB1CYcpoPcOjKy1PiATlC3UsBaLEXsok1xxtRMQ283r282tkh8XQitsxtTczAJBxijuJNfziYhci2jResWXK51ygOOEbVAxmpflujkJ8oEVHkOA/CjX6bGx05pNZ6oSIu9H8deF94MyqIwcdeirCe60GbIQByQtLimfxbIZnO35X3fs/94av0ODfELqrQEpLrpU6FNeHttvlMc5UVrT4K+8lPbqR8Hq0PFWmFrbVIYSi7tAVFMMe2D1C59NWyLu3AkrD3No7YhLVh7LV0Tttr/8FrcZ8xirBPcMZCIGrRIesrHxOsZH2V8t/t0GXCnLLAWX+TNvdNXkB8cF2y9ZXf1enI064yE5dwMs2fQ0yOUG/xornE";
            var options = new Stimulsoft.Viewer.StiViewerOptions();
            options.appearance.fullScreenMode = true;
            options.toolbar.showSendEmailButton = false;
            options.toolbar.showOpenButton = false;
            options.toolbar.printDestination = Stimulsoft.Viewer.StiPrintDestination.Direct;
            options.exports.showExportToDocument = false;
            options.exports.showExportToPdf = true;
            options.exports.showExportToHtml = true;
            options.exports.showExportToHtml5 = true;
            options.exports.showExportToWord2007 = true;
            options.exports.showExportToExcel2007 = false;
            options.exports.showExportToCsv = false;
            
            var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);
		
            // Process SQL data source
            viewer.onBeginProcessData = function (event, callback) {
                jsHelper.process(arguments[0], arguments[1]);
            }
            
            // Load and design report
            var report = new Stimulsoft.Report.StiReport();
            report.loadFile("{{url('reports/'.$coveringletter->type.'.mrt')}}");
            @if($coveringletter->type == 'Resep')
            report.dictionary.variables.getByName("letter_type").value             = "{{$coveringletter->type}}";
            report.dictionary.variables.getByName("patient_name").value            = "{{$coveringletter->patient->name}}";
            report.dictionary.variables.getByName("patient_birth_date").value      = "{{Carbon\Carbon::parse($coveringletter->patient->birth_date)->format('d F Y')}}";
            report.dictionary.variables.getByName("date").value                    = "{{Carbon\Carbon::parse($coveringletter->letter_date)->format('d F Y')}}";
            report.dictionary.variables.getByName("workforce_name").value          = "{{isset($coveringletter->workforce->name)?$coveringletter->workforce->name:'Tidak Ada'}}";
            report.dictionary.variables.getByName("partner_name").value            = "{{isset($coveringletter->partner->name)?$coveringletter->partner->name:'-'}}";
            report.dictionary.variables.getByName("medicine_name").value           = "{{isset($coveringletter->medicine->name)?$coveringletter->medicine->name:'-'}}";
            report.dictionary.variables.getByName("using_rule").value              = "{{isset($coveringletter->usingrule->description)?$coveringletter->usingrule->description:'-'}}";
            report.dictionary.variables.getByName("companyname").value             = "{{config('configs.company_name')}}";
            report.dictionary.variables.getByName("doctor_name").value             = "{{isset($coveringletter->doctor->name)?$coveringletter->doctor->name:'-'}}";
            report.dictionary.variables.getByName("companylogo").value             = "{{url(config('configs.app_logo'))}}";
            report.dictionary.variables.getByName("letter_number").value           = "{{sprintf('%03d', $coveringletter->number)}} Kt/451/UPGRK/{{date('Y')}}";
            
            @elseif($coveringletter->type == 'Diagnosa')
            report.dictionary.variables.getByName("letter_type").value             = "{{$coveringletter->type}}";
            report.dictionary.variables.getByName("patient_name").value            = "{{$coveringletter->patient->name}}";
            report.dictionary.variables.getByName("patient_birth_date").value      = "{{Carbon\Carbon::parse($coveringletter->patient->birth_date)->format('d F Y')}}";
            report.dictionary.variables.getByName("date").value                    = "{{Carbon\Carbon::parse($coveringletter->letter_date)->format('d F Y')}}";
            report.dictionary.variables.getByName("workforce_name").value          = "{{isset($coveringletter->workforce->name)?$coveringletter->workforce->name:'Tidak Ada'}}";
            report.dictionary.variables.getByName("medicine_name").value           = "{{isset($coveringletter->medicine->name)?$coveringletter->medicine->name:'-'}}";
            report.dictionary.variables.getByName("using_rule").value              = "{{isset($coveringletter->usingrule->description)?$coveringletter->usingrule->description:'-'}}";
            report.dictionary.variables.getByName("companyname").value             = "{{config('configs.company_name')}}";
            report.dictionary.variables.getByName("result_consultation").value     = "{{isset($coveringletter->consultation->complaint)?$coveringletter->consultation->complaint:''}}";
            report.dictionary.variables.getByName("doctor_name").value             = "{{isset($coveringletter->doctor->name)?$coveringletter->doctor->name:'-'}}";
            report.dictionary.variables.getByName("companylogo").value             = "{{url(config('configs.app_logo'))}}";
            report.dictionary.variables.getByName("letter_number").value           = "{{sprintf('%03d', $coveringletter->number)}} Kt/451/UPGRK/{{date('Y')}}";
            
           @else
            report.dictionary.variables.getByName("letter_type").value             = "{{$coveringletter->type}}";
            report.dictionary.variables.getByName("referral_doctor").value         = "{{isset($coveringletter->referraldoctor->name)?$coveringletter->referraldoctor->name:'-'}}";
            report.dictionary.variables.getByName("referral_partner_name").value   = "{{isset($coveringletter->referralpartner)?$coveringletter->referralpartner->name:''}}";
            report.dictionary.variables.getByName("patient_name").value            = "{{$coveringletter->patient->name}}";
            report.dictionary.variables.getByName("patient_birth_date").value      = "{{Carbon\Carbon::parse($coveringletter->patient->birth_date)->format('d F Y')}}";
            report.dictionary.variables.getByName("workforce_name").value          = "{{isset($coveringletter->workforce->name)?$coveringletter->workforce->name:'Tidak Ada'}}";
            report.dictionary.variables.getByName("result_consultation").value     = "{{isset($coveringletter->consultation->complaint)?$coveringletter->consultation->complaint:''}}";
            report.dictionary.variables.getByName("medicine_name").value           = "{{isset($coveringletter->medicine->name)?$coveringletter->medicine->name:'-'}}";
            report.dictionary.variables.getByName("using_rules_description").value = "{{isset($coveringletter->usingrule->description)?$coveringletter->usingrule->description:'-'}}";
            report.dictionary.variables.getByName("companyname").value             = "{{config('configs.company_name')}}";
            report.dictionary.variables.getByName("referral_doctor_received").value = "{{isset($coveringletter->referraldoctor->name)?$coveringletter->referraldoctor->name:'-'}}";
            report.dictionary.variables.getByName("doctor_name").value             = "{{isset($coveringletter->doctor->name)?$coveringletter->doctor->name:'-'}}";
            report.dictionary.variables.getByName("companylogo").value             = "{{url(config('configs.app_logo'))}}";
            report.dictionary.variables.getByName("letter_number").value           = "{{sprintf('%03d', $coveringletter->number)}} Kt/451/UPGRK/{{date('Y')}}";
            @endif
            viewer.report = report;
            
            viewer.renderHtml("viewerContent");
        </script>
	</head>
    <body>
        <div id="viewerContent"></div>
    </body>
</html> 
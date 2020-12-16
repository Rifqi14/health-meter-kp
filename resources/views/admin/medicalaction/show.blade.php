
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Desain Tindakan - {{config('configs.app_name')}}</title>

        <!-- Report Office2013 style -->
        <link href="{{asset('adminlte/component/stimulsoft/css/stimulsoft.viewer.office2013.whiteblue.css')}}" rel="stylesheet">
        <link href="{{asset('adminlte/component/stimulsoft/css/stimulsoft.designer.office2013.whiteblue.css')}}" rel="stylesheet">

        <!-- Stimusloft Reports.JS -->
        <script src="{{asset('adminlte/component/stimulsoft/js/stimulsoft.reports.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminlte/component/stimulsoft/js/stimulsoft.viewer.js')}}" type="text/javascript"></script>
        <script src="{{asset('adminlte/component/stimulsoft/js/stimulsoft.designer.js')}}" type="text/javascript"></script>
        
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
            var options = new Stimulsoft.Designer.StiDesignerOptions();
            options.appearance.fullScreenMode = true;
            options.toolbar.showSendEmailButton = true;
            
            var designer = new Stimulsoft.Designer.StiDesigner(options, "StiDesigner", false);
            
            designer.onBeginProcessData = function (event, callback) {
                jsHelper.process(arguments[0], arguments[1]);
            }
            
            designer.onSaveReport = function (event) {
                jsHelper.process(arguments[0], arguments[1]);
            }
            
            // Load and design report
            var report = new Stimulsoft.Report.StiReport();
            report.loadFile("{{url('reports/'.$medicalaction->code.'.mrt')}}");
            designer.report = report;
            designer.renderHtml("designerContent");
        </script>
	</head>
    <body>
        <div id="designerContent"></div>
    </body>
</html>

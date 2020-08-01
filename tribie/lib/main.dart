import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:flutter_webview_plugin/flutter_webview_plugin.dart';
import 'package:flutter/services.dart';
void main() {
  runApp(Home());
}

class Home extends StatefulWidget {

  @override
  _HomeState createState() => _HomeState();
}

class _HomeState extends State<Home> {
  @override
  @override
  void initState() {
    SystemChrome.setEnabledSystemUIOverlays([]);
    super.initState();
  }

  Widget build(BuildContext context) {
    return MaterialApp(

      title: "TriBie",
      home: WebviewScaffold(
        primary: false,
        url: "https://tribie.in",clearCache: false,withJavascript: true,scrollBar: true,withLocalStorage: true,geolocationEnabled: true,
      ),
    );

  }
}

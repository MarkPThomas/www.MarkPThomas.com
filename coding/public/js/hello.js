'use strict';

// In production, the bundled pdf.js shall be used instead of SystemJS.
//Promise.all([System.import('pdfjs/display/api'),
//             System.import('pdfjs/display/worker_options'),
//             System.import('pdfjs/display/network'),
//             System.resolve('pdfjs/worker_loader')])
//       .then(function (modules) {
//  var api = modules[0];
//  var GlobalWorkerOptions = modules[1].GlobalWorkerOptions;
//  var network = modules[2];
//  api.setPDFNetworkStreamFactory((params) => {
//    return new network.PDFNetworkStream(params);
//  });

  // In production, change this to point to the built `pdf.worker.js` file.
  //GlobalWorkerOptions.workerSrc = modules[3];

//var api = window['pdfjs-dist/build/pdf']; //modules[0];

  // Fetch the PDF document from the URL using promises.
  pdfjsLib.getDocument('../projects-data/helloworld.pdf').then(function (pdf) {
    // Fetch the page.
    pdf.getPage(1).then(function (page) {
      var scale = 1.5;
      var viewport = page.getViewport(scale);

      // Prepare canvas using PDF page dimensions.
      var canvas = document.getElementById('the-canvas');
      var context = canvas.getContext('2d');
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      // Render PDF page into canvas context.
      var renderContext = {
        canvasContext: context,
        viewport: viewport
      };
      page.render(renderContext);
    });
  });
//});

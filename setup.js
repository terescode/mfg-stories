/* eslint-env node */

var https = require('https'),
  fs = require('fs'),
  crypto = require('crypto'),
  spawn = require('child_process').spawn,
  composerSetup = 'composer-setup.php',
  expectedSig;

function downloadSig(callback) {
  'use strict';
  var data = '';

  https.get('https://composer.github.io/installer.sig', function (response) {
    response.on('end', function () {
      expectedSig = data.trim();
      callback(null);
    }).on('error', function (err) {
      callback(err);
    }).on('data', function (chunk) {
      data += chunk.toString();
    });
  }).on('error', function (err) {
    callback(err);
  });
}

function downloadComposer(path, callback) {
  'use strict';
  var file = fs.createWriteStream(path);

  https.get('https://getcomposer.org/installer', function (response) {
    response.on('end', function () {
      callback(null);
    }).on('error', function (err) {
      file.end();
      callback(err);
    });
    response.pipe(file);
  }).on('error', function (err) {
    file.end();
    callback(err);
  });
}

function hashFile(path, callback) {
  'use strict';
  var file = fs.createReadStream(path),
    hash = crypto.createHash('sha384');
  hash.setEncoding('hex');

  file.on('end', function () {
    hash.end();
    callback(null, hash.read());
  }).on('error', function (err) {
    callback(err);
  });

  file.pipe(hash);
}

function runComposerSetup(callback) {
  'use strict';
  var php = spawn('php', [composerSetup]);
  php.on('close', function (code) {
    if (0 === code) {
      callback(null);
    } else {
      callback(new Error('php exited with error: ' + code));
    }
  }).on('error', function (err) {
    callback(err);
  });
  php.stderr.on('data', function (data) {
    console.log(data.toString());
  });
  php.stdout.on('data', function (data) {
    console.log(data.toString());
  });
}

function cleanup(msg) {
  'use strict';
  if (fs.existsSync(composerSetup)) {
    fs.unlinkSync(composerSetup);
  }
  console.log(msg);
}

downloadSig(function (err) {
  'use strict';
  if (err) {
    cleanup('Download signature failed with:' + err);
  } else {
    downloadComposer(composerSetup, function (err) {
      if (err) {
        cleanup('Download composer failed with: ' + err);
      } else {
        hashFile(composerSetup, function (err, hash) {
          if (err) {
            cleanup('Read hash failed with: ' + err);
          } else {
            if (expectedSig !== hash) {
              cleanup('Installer corrupt!');
            } else {
              runComposerSetup(function (err) {
                if (err) {
                  cleanup('Run composer failed with: ' + err);
                } else {
                  cleanup('Done');
                }
              });
            }
          }
        });
      }
    });
  }
});



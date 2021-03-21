#!/bin/bashx

#使い方 : $ bash ./bin/update.sh 1-0-0

#引数 : プラグインのバージョン
version=$1
version_num=${version//-/.}

# バージョン書き換え
sed -i '' -e "s/Version: .*/Version: ${version_num}/g" pochipp.php;

#上の階層へ
cd ..

#zプラグインファイルをip化
zip -r pochipp.zip pochipp -x "*._*" "*__MACOSX*" "*.DS_Store" "*.git*" "*.vscode*" "*/_nouse/*" "*/src/**/*.js" "*/bin/*" "*/vendor/*" "*gulpfile.js" "*webpack.config.*" "*/node_modules/*" "*package.json" "*package-lock.json" "*composer.json" "*composer.lock" "*README.md" "*postcss.config.js" "*memo.md" "*phpcs.xml"

#設定ファイル系削除
zip --delete pochipp.zip  "pochipp/.*"

#zipファイルを移動
mv pochipp.zip ./pochipp-${version}.zip
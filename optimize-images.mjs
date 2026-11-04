/**
 * src/img 配下の画像を圧縮して dist/img へ出力する（ディレクトリ構造は維持）。
 * JPEG は mozjpeg、PNG はパレット化（libimagequant）で圧縮し、
 * それ以外の拡張子（svg / gif など）はそのままコピーする。
 */
import { copyFile, mkdir, readdir } from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const SRC_DIR = 'src/img';
const DIST_DIR = 'dist/img';

/**
 * ディレクトリ配下のファイルパスを再帰的に取得する。
 *
 * @param {string} dir 対象ディレクトリ
 * @returns {Promise<string[]>}
 */
async function listFiles( dir ) {
	const entries = await readdir( dir, { withFileTypes: true } );
	const files = await Promise.all(
		entries.map( ( entry ) => {
			const fullPath = path.join( dir, entry.name );
			return entry.isDirectory() ? listFiles( fullPath ) : [ fullPath ];
		} )
	);
	return files.flat();
}

/**
 * 1 ファイルを圧縮（または複製）して出力する。
 *
 * @param {string} srcPath 入力ファイルパス
 */
async function optimize( srcPath ) {
	const distPath = path.join( DIST_DIR, path.relative( SRC_DIR, srcPath ) );
	await mkdir( path.dirname( distPath ), { recursive: true } );

	try {
		switch ( path.extname( srcPath ).toLowerCase() ) {
			case '.jpg':
			case '.jpeg':
				await sharp( srcPath ).jpeg( { mozjpeg: true, quality: 75 } ).toFile( distPath );
				break;
			case '.png':
				await sharp( srcPath ).png( { palette: true, compressionLevel: 9 } ).toFile( distPath );
				break;
			default:
				await copyFile( srcPath, distPath );
		}
	} catch ( error ) {
		throw new Error( `${ srcPath }: ${ error.message }`, { cause: error } );
	}
}

try {
	const files = ( await listFiles( SRC_DIR ) ).filter(
		( file ) => ! path.basename( file ).startsWith( '.' )
	);
	await Promise.all( files.map( optimize ) );
	console.log( `Images optimized! (${ files.length } files)` );
} catch ( error ) {
	console.error( `画像の最適化に失敗しました: ${ error.message }` );
	process.exitCode = 1;
}

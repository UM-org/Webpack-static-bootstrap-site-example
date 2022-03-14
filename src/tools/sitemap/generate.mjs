import {
    generateUrls,
    generateXmlSitemap,
} from 'static-sitemap-cli';
import * as fs from "fs";

const options = {
    base: process.env.SITE_URL ?? "https://example.com/",
    root: './public/pages',
    match: ['**/*html'],
    ignore: ['404.html'],
    changefreq: [],
    priority: [],
    robots: true,
    concurrent: 128,
    clean: false,
    slash: false,
    stdout: "./public"
}

generateUrls(options).then((urls) => {
    const xmlString = generateXmlSitemap(urls);
    var ws = fs.createWriteStream('./public/sitemap.xml');
    ws.write(xmlString, 'UTF-8');
    ws.end();
    console.info('Sitemap created.');
}).catch((err) => {
    console.error("Sitemap failed ", err);
});
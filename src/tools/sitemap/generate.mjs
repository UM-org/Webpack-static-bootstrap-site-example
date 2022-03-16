import {
    generateUrls,
    generateXmlSitemap,
} from 'static-sitemap-cli';
import * as fs from "fs";

const site =process.env.SITE_URL ?? "https://example.com/";

const options = {
    base: site,
    root: './src/pages',
    match: ['**/*html'],
    ignore: ['404.html'],
    changefreq: [],
    priority: [
        "index.html,0.9"
    ],
    robots: true,
    concurrent: 128,
    clean: true,
    slash: false,
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
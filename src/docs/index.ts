/*
import { cloneDeep, keys } from "lodash";
import * as path from "path";
import { Server } from "restify";
import * as restify from "restify";
import * as YAML from "yamljs";

const swaggerUi = require("swagger-ui-restify");

export const authorization = YAML.load("./src/docs/authorization.yaml");
export const calculation = YAML.load("./src/docs/calculation.yaml");
export const participant = YAML.load("./src/docs/participant.yaml");
export const user = YAML.load("./src/docs/user.yaml");

const PATH_TO_DOCS = path.resolve(__dirname, "./");

export const loadDocs = async (server: Server): Promise<void> => {
	const docs = await import(PATH_TO_DOCS);
	for (const key of keys(docs)) {
		const doc = docs[key];
		server.get(`/doc/${key}`, (res, req, next) => {
			return swaggerUi.setup(cloneDeep(doc))(res, req, next);
		});
	}
	server.get("/doc/!*", swaggerUi.serve);
	server.get("/doc", restify.plugins.serveStatic({
		directory: path.resolve("./"),
		file: "api.html",
	}));
};
*/

import { ApiError } from "@api-errors/ApiError";
import { EHttpStatus } from "@api-errors/EHttpStatus";
import { EMethod } from "@core/routes/interfaces/IRouteDefinition";
import * as Joi from "@hapi/joi";
import { attempt, get, merge } from "lodash";
import { Next, Request, Response } from "restify";

export const validateMiddleware = (schema: Joi.Schema) => {
	if (!schema) {
		return;
	}
	return (req: Request, res: Response, next: Next) => {
		const body = req.method === EMethod.GET ? attempt(JSON.parse, req.body) : req.body;
		const params = merge(req.params, req.query, body);
		Joi.validate(params, schema, (err) => {
			if (err) {
				res.send(parseError(err));
				return next(err);
			} else {
				return next();
			}
		});
	};
};

const parseError = (error: Joi.Err) => {
	if (!error.isJoi) {
		return error;
	}
	const message = `Проверьте запрос отправленный серверу. ${get(error, "details[0].message", "")}`;
	return new ApiError(EHttpStatus.BAD, message, 100);
};

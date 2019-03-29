import * as Joi from "joi";
import { ERoles } from "../middleware/interfaces/ERoles";

export default {
	"POST /api/authorization/login": {
		handler: "AuthorizationController.login",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			login: Joi.string().required(),
			password: Joi.string().required(),
		})
	},
	"POST /api/authorization/registration": {
		handler: "AuthorizationController.registration",
		allowRoles: [ERoles.GLOBAL_ADMIN],
		validate: Joi.object().keys({
			login: Joi.string().required(),
			password: Joi.string().required(),
			email: Joi.string().required(),
			role: Joi.string().required(),
		})
	}
};

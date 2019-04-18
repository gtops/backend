import * as Joi from "joi";
import { ERoles } from "../middleware/interfaces/ERoles";

export default {
	"POST /api/authorization/login": {
		handler: "AuthorizationController.login",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			login: Joi.string().required(),
			password: Joi.string().required()
		})
	},
	"POST /api/authorization/registration": {
		handler: "AuthorizationController.registration",
		allowRoles: [ERoles.INVITED_USER],
		validate: Joi.object().keys({
			login: Joi.string().required(),
			password: Joi.string().required(),
			email: Joi.string().required()
		})
	},
	"POST /api/authorization/invite": {
		handler: "AuthorizationController.invite",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			email: Joi.string().required(),
			role_id: Joi.number().required()
		})
	}
};

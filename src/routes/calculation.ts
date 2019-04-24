import * as Joi from "joi";
import { ERoles } from "../middleware/interfaces/ERoles";

export default {
	"POST /api/participant/trial": {
		handler: "CalculationController.getParticipantTrial",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			gender_id: Joi.number().required(),
			age_sign: Joi.object().keys({
				old: Joi.date(),
				age_category_id: Joi.number()
			}).required(),
		})
	},
	"POST /api/calculate": {
		handler: "CalculationController.calculate",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			trial_id: Joi.number().required(),
			gender_id: Joi.number().required(),
			age_category_id: Joi.number().required(),
			primary_result: Joi.number().required(),
		})
	}
};

import * as Joi from "joi";
import { ERoles } from "@middleware/interfaces/ERoles";

export default {
	"GET /api/participant/:uid": {
		handler: "ParticipantController.getDataParticipant",
		allowRoles: [ERoles.GUEST],
		validate: Joi.object().keys({
			uid: Joi.string().required(),
		})
	},
	"GET /api/participant/categories": {
		handler: "ParticipantController.getListAgeCategories",
		allowRoles: [ERoles.GUEST]
	}
};

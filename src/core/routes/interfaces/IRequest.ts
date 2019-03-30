import * as restify from "restify";
import { ERoles } from "../../../middleware/interfaces/ERoles";

export interface IRequest extends restify.Request {
	user_id?: number;
	role: ERoles;
}

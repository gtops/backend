import { AgeCategory } from "./age-category/AgeCategory";
import { Command } from "./command/Command";
import { Competition } from "./competition/Competition";
import { Gender } from "./gender/Gender";
import { GroupInStandardParent } from "./group-in-standard-parent/GroupInStandardParent";
import { ParticipantOnCompetition } from "./participant-on-competition/ParticipantOnCompetition";
import { Participant } from "./participant/Participant";
import { Position } from "./position/Position";
import { RefereeOnTrialInCompetition } from "./referee-on-trial-in-competition/RefereeOnTrialInCompetition";
import { ResultGuide } from "./result-guide/ResultGuide";
import { ResultParticipantOnTrial } from "./result-participant-on-trial/ResultParticipantOnTrial";
import { Role } from "./role/Role";
import { StandardParent } from "./standard-parent/StandardParent";
import { TrialOnCompetition } from "./trial-on-competition/TrialOnCompetition";
import { TrialOnGroup } from "./trial-on-group/TrialOnGroup";
import { Trial } from "./trial/Trial";
import { Unit } from "./unit/Unit";
import { User } from "./user/User";
import { WorkerOfUserInCompetition } from "./worker-of-user-in-competition/WorkerOfUserInCompetition";
import { WorkerOfUser } from "./worker-of-user/WorkerOfUser";

export const models = [
	User,
	AgeCategory,
	Competition,
	Command,
	Gender,
	Unit,
	TrialOnCompetition,
	TrialOnGroup,
	Trial,
	StandardParent,
	Role,
	GroupInStandardParent,
	Participant,
	ParticipantOnCompetition,
	ResultGuide,
	ResultParticipantOnTrial,
	Position,
	WorkerOfUser,
	WorkerOfUserInCompetition,
	RefereeOnTrialInCompetition
];
